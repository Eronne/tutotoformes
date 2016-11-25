<?php

namespace AppBundle\Controller;

use AppBundle\AchievementsName;
use AppBundle\Entity\UserProgression;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurAchievementAssociation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
        $tutoriels = $this->getDoctrine()->getRepository('AppBundle:Tutoriel')->getFirstNth(5, 'DESC', false);
        return $this->render('index.html.twig', ['last_tutoriels' => $tutoriels]);
    }

    /**
     * @Route(name="menu")
     */
    public function menuAction($inverted = false) {
        $pages = [];
        $requestStack = $this->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();
        return $this->render('partials/_menu.html.twig', ['pages' => $pages, 'inverted' => $inverted, 'route_name' => $masterRequest->attributes->get('_route')]);
    }

    /**
     * @param Request $request
     * @Route("/cgu", name="cgu")
     */
    public function showCguAction(Request $request) {
        return $this->render('cgu.html.twig');
    }



}
