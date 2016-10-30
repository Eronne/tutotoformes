<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use AppBundle\Entity\UserProgression;
use AppBundle\Entity\Utilisateur;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TutorielPageController extends Controller
{
    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @return \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws Exception
     * @Route("/admin/tutoriel/{id}/page/add", name="admin_tutoriel_page_add")
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_WRITER') and tutoriel.getAuthor() == user)")
     */
    public function addAction(Request $request, Tutoriel $tutoriel){
        if(!$tutoriel){
            return $this->createNotFoundException();
        }
        if($request->getMethod() == "GET"){
            return $this->render('tutoriel/page/add.html.twig', ['tutoriel' => $tutoriel]);
        } else {
            $params = $request->get('_tutorielpage');

            if (!$params) {
                throw new Exception('Le paramètre "tutorielpage" n a pas été trouvé dans les paramètres de la requête');
            }

            $em = $this->getDoctrine()->getManager();

            $tutorielPage = new TutorielPage();
            $html = new \DOMDocument();
            $html->loadHTML($params['content']);
            $h2 = $html->getElementsByTagName('h2');
            for($i = 0; $i < $h2->length; $i++){
                $h2->item($i)->setAttribute('id', self::slugify($h2->item($i)->nodeValue));
            }
            $tutorielPage->setCreatedAt(new \DateTime('now'))
                ->setTitle($params['title'])
                ->setPageNumber($params['page_number'])
                ->setContent($html->saveHTML())
                ->setTutoriel($tutoriel);

            $userProgressions = $this->getDoctrine()->getRepository('AppBundle:UserProgression')->findBy(['tutoriel' => $tutoriel]);
            foreach($userProgressions as $userProgression) {
                $userProgression->setProgression(round(count($userProgression->getCompletedPages()) / $tutoriel->getTutorialPages()->count() * 100));
            }

            $em->persist($tutorielPage);
            $em->flush();

            $this->addFlash('notification success', "La page a bien été ajouté. <a href='" . $this->get('router')->generate('tutoriel_show',
                    ['slug' => $tutoriel->getSlug(), 'slug_page' => $tutorielPage->getSlug()]) ."'>Voir le tutoriel</a>");
            return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);
        }
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @param $slug_page
     * @return \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws Exception
     * @Route("/admin/tutoriel/edit/{id}/page/{slug_page}", name="admin_tutoriel_page_edit")
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_WRITER') and tutoriel.getAuthor() == user)")
     */
    public function editAction(Request $request, Tutoriel $tutoriel, $slug_page){
        /** @var TutorielPage $tutorielPage */
        $tutorielPage = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['slug' => $slug_page]);

        if(!$tutoriel && !$tutorielPage){
            return $this->createNotFoundException();
        }
        if($request->getMethod() == "GET"){
            return $this->render('tutoriel/page/edit.html.twig', ['tutoriel' => $tutoriel, 'page' => $tutorielPage]);
        } else {
            $params = $request->get('_tutorielpage');
            if (!$params) {
                throw new Exception('Le paramètre "tutorielpage" n a pas été trouvé dans les paramètres de la requête');
            }
            $html = new \DOMDocument();
            $html->loadHTML($params['content']);
            $h2 = $html->getElementsByTagName('h2');
            for($i = 0; $i < $h2->length; $i++){
                $h2->item($i)->setAttribute('id', self::slugify($h2->item($i)->nodeValue));
            }
            $tutorielPage->setEditedAt(new \DateTime('now'))
                ->setContent($html->saveHTML())
                ->setPageNumber($params['page_number'])
                ->setTitle($params['title']);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notification success', "La page a bien été modifié.");
            return $this->redirectToRoute('tutoriel_show', ['slug' => $tutoriel->getSlug(), 'slug_page' => $tutorielPage->getSlug()]);

        }
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @param string $slug_page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Route("/admin/tutoriel/{id}/remove/{slug_page}", name="admin_tutoriel_page_remove")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removeAction(Request $request, Tutoriel $tutoriel, $slug_page){
        $pageRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:TutorielPage');
        $tutorielPage = $pageRepo->findOneBy(['slug' => $slug_page, 'tutoriel' => $tutoriel]);
        if(!$tutoriel && !$tutorielPage){
            return $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();


        $prevPage = $pageRepo->findOneBy(['pageNumber' => $tutorielPage->getPageNumber() - 1, 'tutoriel' => $tutoriel]);
        $nextPage = $pageRepo->findOneBy(['pageNumber' => $tutorielPage->getPageNumber() + 1, 'tutoriel' => $tutoriel]);

        $userProgression = $tutoriel->getUserProgression($this->get('security.token_storage')->getToken()->getUser());

        if(!$prevPage && $nextPage) {
            foreach ($pageRepo->findAll() as $page){
                $page->setPageNumber($page->getPageNumber() - 1);
            }
        }else if($nextPage && $prevPage) {
            foreach ($pageRepo->findAllSuperiorToPage($tutorielPage->getPageNumber()) as $page){
                $page->setPageNumber($page->getPageNumber() - 1);
            }
        }

        if (in_array($tutorielPage->getSlug(), $userProgression->getCompletedPages())) {
            $array = $userProgression->getCompletedPages();
            $key = array_search($tutorielPage->getSlug(), $array);
            unset($array[$key]);
            $userProgression->setCompletedPages($array);
        }
        $em->remove($tutorielPage);
        $em->flush();

        $this->UpdateUserProgression($tutoriel,$tutorielPage, $this->get('security.token_storage')->getToken()->getUser(), true);
        $this->addFlash('notification success', "La page a bien été supprimé");
        return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);
    }

    static private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
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
        } else {
            $userProgression->setFinishedAt(new \DateTime('now'));
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





}
