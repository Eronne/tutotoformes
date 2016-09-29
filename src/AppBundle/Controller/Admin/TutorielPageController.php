<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
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
     * @Route("/admin/tutoriel/{id}/page/add", name="admin_tutoriel_page_add")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction(Request $request, Tutoriel $tutoriel){
        if(!$tutoriel){
            return $this->createNotFoundException();
        }
        if($request->getMethod() == "GET"){
            return $this->render('tutoriel/page/add.html.twig', ['tutoriel' => $tutoriel]);
        } else {
            $tutorielPage = $this->get('app.utils')->createEntityFromParameters('AppBundle:TutorielPage', $request);
            $em = $this->getDoctrine()->getManager();
            $tutorielPage->setTutoriel($tutoriel);
            $em->persist($tutorielPage);
            $em->flush();
            $this->addFlash('notification success', 'La page a bien été ajouté');
            return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);
        }
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @return \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Route("/admin/tutoriel/{id}/page/{slug_page}", name="admin_tutoriel_page_edit")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Tutoriel $tutoriel, $slug_page){
        $tutorielPage = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['slug' => $slug_page]);
        if(!$tutoriel && !$tutorielPage){
            return $this->createNotFoundException();
        }
        if($request->getMethod() == "GET"){
            return $this->render('tutoriel/page/edit.html.twig', ['tutoriel' => $tutoriel, 'page' => $tutorielPage]);
        } else {
            $this->get('app.utils')->updateEntityFromParameters($tutorielPage, $request);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notification success', "La page a bien été modifié");
            return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);

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
