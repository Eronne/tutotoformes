<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('test.html.twig');
    }

    /**
     * @Route(name="menu")
     */
    public function menuAction($inverted = false) {
        $pages = [];
        return $this->render('partials/_menu.html.twig', ['pages' => $pages, 'inverted' => $inverted]);
    }

}
