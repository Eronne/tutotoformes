<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TutorielController
 * @package AppBundle\Controller\Admin
 * @Route("/admin/tutoriel")
 */
class TutorielController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="tutoriel_index")
     */
    public function indexAction(Request $request)
    {
        $tuto = new Tutoriel();
        $pageTutoriel = new TutorielPage();
        $tuto->addTutorialPage($pageTutoriel);
        return $this->render('tutoriel/index.html.twig');
    }
}
