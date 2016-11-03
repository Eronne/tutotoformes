<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TutorielController extends Controller
{
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
