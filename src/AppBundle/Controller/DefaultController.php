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
    public function homepageAction(Request $request){
        if(!$this->getUser())
            return $this->render('homepage.html.twig');
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/home", name="index")
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
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request) {
        if($request->getMethod() == "GET"){
            return $this->render('contact.html.twig');
        } else {
            $admins = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findByRoles(['ROLE_ADMIN']);
            $to = [];
            foreach ($admins as $admin) {
                array_push($to, $admin->getEmail());
            }
            $message = $request->get('_message');
            $subject = $request->get('_subject');
            $user = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findOneBy(['email' => $request->get('_email')]);
            if(!$user) throw $this->createNotFoundException();
            $this->get('app.utils')->sendMail($subject, 'contact@tutotoformes.fr', 'Tutotoformes', $to, $this->renderView('mails/contact.html.twig', ['user' => $user, 'message' => $message]), 'text/html');
            $this->addFlash('notification success', 'Le message a bien été envoyé');
            return $this->redirectToRoute('contact');
        }
    }



}
