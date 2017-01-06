<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use AppBundle\Entity\UserProgression;
use AppBundle\Entity\Utilisateur;
use AppBundle\Events\TutorielEvents;
use AppBundle\Events\TutorielFinishedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TutorielController extends Controller
{
    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @Route("/tutoriel/{slug}", name="tutoriel_summary_show")
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showSummaryAction(Request $request, Tutoriel $tutoriel)
    {

        if (!$tutoriel) {
            return $this->createNotFoundException("Le tutoriel n'a pas été trouvé");
        }
        $pageRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:TutorielPage');

        $nextPage = $pageRepo->findOneBy(['pageNumber' => 1, 'tutoriel' => $tutoriel]);

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $authorIds = [];
        foreach ($tutoriel->getAuthors() as $author) {
            array_push($authorIds, $author->getId());
        }

        if($user instanceof Utilisateur ){

            $up = $tutoriel->getUserProgression($user);


            if($up){
                $lastPageCompleted = null;

                if($user && count($up->getCompletedPages()) > 0){
                    $lastPageCompleted = $pageRepo->getLastUnreadedSlug($tutoriel, $user);
                }
                return $this->render('tutoriel/summary.html.twig', ['author_ids' => $authorIds, 'tutoriel' => $tutoriel, 'next_page' => $nextPage, 'last_unreaded_slug' => $lastPageCompleted]);

            } else {
                return $this->render('tutoriel/summary.html.twig', ['author_ids' => $authorIds, 'tutoriel' => $tutoriel, 'next_page' => $nextPage]);
            }



        } else {
            return $this->render('tutoriel/summary.html.twig', ['author_ids' => $authorIds, 'tutoriel' => $tutoriel, 'next_page' => $nextPage]);
        }




    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @Route("/tutoriel/{slug}/{slug_page}", name="tutoriel_show")
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request, Tutoriel $tutoriel, $slug_page)
    {
        if (!$tutoriel) {
            return $this->createNotFoundException();
        }
        $authorIds = [];
        foreach ($tutoriel->getAuthors() as $author) {
            array_push($authorIds, $author->getId());
        }
        $page = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['slug' => $slug_page, 'tutoriel' => $tutoriel]);
        if (!$page) {
            return $this->createNotFoundException();
        }

        $user = $this->getUser();

        if(!$user instanceof Utilisateur & $page->getPageNumber() > 1) {
            $this->addFlash('notification info', "Afin de lire la suite du tutoriel, veuillez-vous inscrire ou vous connecter.");
            return $this->redirectToRoute('login', ['redirect' => $this->get('router')->generate('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $page->getSlug()])]);
        }

        $pageRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:TutorielPage');

        $prevPage = $pageRepo->findOneBy(['pageNumber' => $page->getPageNumber() - 1, 'tutoriel' => $tutoriel]);
        $nextPage = $pageRepo->findOneBy(['pageNumber' => $page->getPageNumber() + 1, 'tutoriel' => $tutoriel]);



        return $this->render('tutoriel/page/show.html.twig', ['author_ids' => $authorIds, 'tutoriel' => $tutoriel, 'page' => $page, 'prev_page' => $prevPage, 'next_page' => $nextPage]);
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @param $id_page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param TutorielPage $tutorielPage
     * @Route("/tutoriel/{slug}/comment/{id_page}/new", name="tutoriel_page_add_comment")
     * @Security("has_role('ROLE_USER')")
     */
    public function addCommentAction(Request $request, Tutoriel $tutoriel, $id_page) {
        $tutorielPage = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['id' => $id_page ]);
        if(!$tutorielPage) throw $this->createNotFoundException();
        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime())
            ->setAuthor($this->getUser())
            ->setMessage($request->get('_message'))
            ->setTutorielPage($tutorielPage);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($comment);
        $em->flush();
        $this->addFlash('notification success', 'Votre commentaire a bien été ajouté !');
        return $this->redirectToRoute('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $tutorielPage->getSlug()]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @param $id_page
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param Tutoriel $tutoriel
     * @internal param TutorielPage $tutorielPage
     * @Route("/tutoriel/{slug}/comment/{id_page}/delete/{id}", name="tutoriel_page_delete_comment")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteCommentAction(Request $request, $slug, $id_page, Comment $comment) {
        $tutoriel = $this->getDoctrine()->getRepository('AppBundle:Tutoriel')->findOneBy(['slug' => $slug]);
        $tutorielPage = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['id' => $id_page ]);
        if(!$tutorielPage || !$tutoriel) throw $this->createNotFoundException();
        if($comment->getAuthor() != $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('notification error', "Vous ne pouvez pas supprimer le commentaire d'un autre !");
            return $this->redirectToRoute('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $tutorielPage->getSlug()]);
        }
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($comment);
        $em->flush();
        $this->addFlash('notification success', "Le commentaire a bien été supprimé");
        return $this->redirectToRoute('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $tutorielPage->getSlug()]);
    }

    /**
     * @param Request $request
     * @param $tutoriel
     * @param $slug_page
     * @Route("/tutoriel/{slug}/has-completed/{slug_page}",name="tutoriel_mark_page_as_complete")
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markPartAsCompleteAction(Request $request, Tutoriel $tutoriel, $slug_page)
    {
        $page = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['slug' => $slug_page, 'tutoriel' => $tutoriel]);

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userProgression = $tutoriel->getUserProgression($user);

        if (!$userProgression) {
            $up = new UserProgression();
            $up->setTutoriel($tutoriel)
                ->setProgression(0)
                ->setUser($user);
            $this->getDoctrine()->getEntityManager()->persist($up);
            $this->getDoctrine()->getEntityManager()->flush();
            $userProgression = $tutoriel->getUserProgression($user);
        }

        $this->UpdateUserProgression($tutoriel, $page, $user, false);

        $nextPage = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['pageNumber' => $page->getPageNumber() + 1, 'tutoriel' => $tutoriel]);

        if ($userProgression->getProgression() == 100) {
            $event = new TutorielFinishedEvent($this->get('service_container'), $tutoriel, $userProgression);
            $this->get('event_dispatcher')->dispatch(TutorielEvents::FINISHED_TUTORIEL, $event);
            return $this->redirectToRoute('tutoriel_summary_show', ['slug' => $tutoriel->getSlug()]);
        } elseif ($nextPage) {
            return $this->redirectToRoute('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $nextPage->getSlug()]);
        }
        return $this->redirectToRoute('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $page->getSlug()]);

    }

    /**
     * @param Request $request
     * @param $tutoriel
     * @param $slug_page
     * @Route("/tutoriel/{slug}/has-not-completed/{slug_page}",name="tutoriel_unmark_page_as_complete")
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unmarkPartAsCompleteAction(Request $request, Tutoriel $tutoriel, $slug_page)
    {
        $page = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['slug' => $slug_page, 'tutoriel' => $tutoriel]);

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userProgression = $tutoriel->getUserProgression($user);

        if (!$userProgression) {
            $up = new UserProgression();
            $up->setTutoriel($tutoriel)
                ->setProgression(0)
                ->setUser($user);
            $this->getDoctrine()->getEntityManager()->persist($up);
            $this->getDoctrine()->getEntityManager()->flush();
            $userProgression = $tutoriel->getUserProgression($user);
        }

        $this->UpdateUserProgression($tutoriel, $page, $user, true);

        $userProgression->setProgression(round(count($userProgression->getCompletedPages()) / $tutoriel->getTutorialPages()->count() * 100));

        $this->getDoctrine()->getEntityManager()->flush();


        return $this->redirectToRoute('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $page->getSlug()]);

    }

    private function UpdateUserProgression(Tutoriel $tutoriel, TutorielPage $page, Utilisateur $user, $delete){


        $userProgression = $tutoriel->getUserProgression($user);

        if (!$userProgression) {
            $up = new UserProgression();
            $up->setTutoriel($tutoriel)
                ->setProgression(0)
                ->setUser($user);
            $this->getDoctrine()->getEntityManager()->persist($up);
            $this->getDoctrine()->getEntityManager()->flush();
            $userProgression = $tutoriel->getUserProgression($user);
        }

        if(!$delete) {
            if (!in_array($page->getSlug(), $userProgression->getCompletedPages())) {
                $array = $userProgression->getCompletedPages();
                array_push($array, $page->getSlug());
                $userProgression->setCompletedPages($array);
            }
        } else {
            if (in_array($page->getSlug(), $userProgression->getCompletedPages())) {
                $array = $userProgression->getCompletedPages();
                $key = array_search($page->getSlug(), $array);
                unset($array[$key]);
                $userProgression->setCompletedPages($array);
            }
        }

        if(count($userProgression->getCompletedPages()) == 0){
            $userProgression->setProgression(0);
        } else {
            $userProgression->setProgression(round(count($userProgression->getCompletedPages()) / $tutoriel->getTutorialPages()->count() * 100));
        }
        if($userProgression->getProgression() != 100){
            $userProgression->setFinishedAt(null);
        }

        if($userProgression->getStartedAt() == null && $tutoriel->getUserProgression($user)->getLastCompletedPageSlug() != null){
            $userProgression->setStartedAt(new \DateTime('now'));
            $this->getDoctrine()->getEntityManager()->flush();
        } else if($userProgression->getStartedAt() != null && $userProgression->getProgression() == 0) {
            $userProgression->setStartedAt(null);
            $this->getDoctrine()->getEntityManager()->flush();
        }
        $this->getDoctrine()->getEntityManager()->flush();
    }

    /**
     * @param Request $request
     * @Route("/tutoriels/", name="tutoriel_list")
     */
    public function searchAction(Request $request)
    {
        if($request->query->count() > 0) {
            return $this->render('tutoriel/list.html.twig');
        } else {
            $tutoriels = $this->getDoctrine()->getRepository('AppBundle:Tutoriel')->findAll();
            return $this->render('tutoriel/list.html.twig', ['tutoriels' => $tutoriels]);
        }

    }
}
