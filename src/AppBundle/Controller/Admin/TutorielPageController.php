<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
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

            $tutorielPage = new TutorielPage();
            $tutorielPage->setCreatedAt(new \DateTime('now'))
                ->setTitle($params['title'])
                ->setPageNumber($params['page_number'])
                ->setContent($params['content'])
                ->setTutoriel($tutoriel);

            $em = $this->getDoctrine()->getManager();
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
            $tutorielPage->setEditedAt(new \DateTime('now'))
                ->setContent($params['content'])
                ->setPageNumber($params['page_number'])
                ->setTitle($params['title']);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notification success', "La page a bien été modifié");
            return $this->redirectToRoute('admin_tutoriel_page_edit', ['id' => $tutoriel->getId(), 'slug_page' => $tutorielPage->getSlug()]);

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

        if(!$prevPage && $nextPage) {
            foreach ($pageRepo->findAll() as $page){
                $page->setPageNumber($page->getPageNumber() - 1);
            }
        }else if($nextPage && $prevPage) {
            foreach ($pageRepo->findAllSuperiorToPage($tutorielPage->getPageNumber()) as $page){
                $page->setPageNumber($page->getPageNumber() - 1);
            }
        }
        $em->remove($tutorielPage);
        $em->flush();
        $this->addFlash('notification success', "La page a bien été supprimé");
        return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);
    }



}
