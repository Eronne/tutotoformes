<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TutorielController
 * @package AppBundle\Controller\Admin
 */
class TutorielController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/tutoriel/", name="admin_tutoriels_index")
     * @Security("has_role('ROLE_ADMIN')")

     */
    public function indexAction(Request $request)
    {
        $tutoriels = $this->getDoctrine()->getRepository('AppBundle:Tutoriel')->findAll();
        return $this->render('tutoriel/index.html.twig', ['tutoriels' => $tutoriels]);
    }

    /**
     * @param Request $request
     * @Route("/admin/tutoriel/add", name="admin_tutoriel_add")
     * @Security("has_role('ROLE_ADMIN')")

     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request){
        if($request->getMethod() == "GET"){
            $users = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findAll();
            return $this->render('tutoriel/add.html.twig', ['users' => $users]);
        } else {
            $tutoriel = $this->get('app.utils')->createEntityFromParameters('AppBundle:Tutoriel', $request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($tutoriel);
            $em->flush();
            $this->addFlash('positive', "Le tutoriel a bien été ajouté !");
            return $this->redirectToRoute('admin_tutoriels_index');
        }
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @Route("/admin/tutoriel/edit/{id}", name="admin_tutoriel_edit")
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Request $request, Tutoriel $tutoriel){
        if(!$tutoriel){
            return $this->createNotFoundException();
        }
        $users = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findAll();
        if($request->getMethod() === "GET"){
            return $this->render('tutoriel/edit.html.twig', ['tutoriel' => $tutoriel, 'users' => $users]);
        } else {
            $em = $this->getDoctrine()->getManager();
            $this->get('app.utils')->updateEntityFromParameters($tutoriel, $request);
            $em->flush();
            $this->addFlash('positive', "Le tutoriel a bien été modifié");

            return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);
        }
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @Route("/admin/tutoriel/remove/{id}", name="admin_tutoriel_remove")
     * @Security("has_role('ROLE_ADMIN')")

     * @return \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeAction(Request $request, Tutoriel $tutoriel){
        if(!$tutoriel){
            return $this->createNotFoundException();
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($tutoriel);
        $em->flush();
        $this->addFlash('positive', "Le tutoriel a bien été supprimé");
        return $this->redirectToRoute('admin_tutoriels_index');
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @Route("/tutoriel/{slug}/{slug_page}", name="tutoriel_show")
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request, Tutoriel $tutoriel, $slug_page) {
        if(!$tutoriel){
            return $this->createNotFoundException();
        }
        $page = $this->getDoctrine()->getRepository('AppBundle:TutorielPage')->findOneBy(['slug' => $slug_page]);
        if(!$page) {
            return $this->createNotFoundException();
        }

        $pageRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:TutorielPage');

        $prevPage = $pageRepo->findOneBy(['pageNumber' => $page->getPageNumber() - 1]);
        $nextPage = $pageRepo->findOneBy(['pageNumber' => $page->getPageNumber() + 1]);


        return $this->render('tutoriel/page/show.html.twig', ['tutoriel' => $tutoriel, 'page' => $page, 'prev_page' => $prevPage, 'next_page' => $nextPage]);
    }
}
