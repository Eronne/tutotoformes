<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\Utilisateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/profile/me", name="my_profile")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Request $request)
    {
        /** @var Utilisateur $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $tutorielRepo = $this->getDoctrine()->getRepository('AppBundle:Tutoriel');
        $followingTutoriels = $tutorielRepo->getTutorielsStartedBy($user, true);



        return $this->render('profile/me.html.twig', ['user' => $user, 'following_tutoriels' => $followingTutoriels]);
    }
}
