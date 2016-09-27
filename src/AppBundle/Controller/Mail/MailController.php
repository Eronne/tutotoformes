<?php

namespace AppBundle\Controller\Mail;

use AppBundle\Entity\Utilisateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller
{

    /**
     * @param Request $request
     * @param Utilisateur $user
     * @Route("/mail/send/verification/{id})", name="email_send_verification")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sendVerificationEmailAction(Request $request, Utilisateur $user){
        if(!$user) {
            return $this->createNotFoundException("Utilisateur non trouvé");
        }
        $user->setToken($this->get('app.utils')->str_random());
        $this->getDoctrine()->getManager()->flush();
        $globals = $this->get('twig')->getGlobals();
        $this->get('app.utils')->sendMail("Vérifier votre adresse email", 'no-reply@' . strtolower($globals['app_name']) . '.fr', $globals['app_name'], $user->getEmail(),
            $this->renderView('mails/validation.html.twig', ['user' => $user]), "text/html");
            $this->addFlash('info', "Un email de vérification vous a été envoyé. Confirmez votre inscription via cet email");
        return $this->redirectToRoute("login");
    }

    /**
     * @param Request $request
     * @Route("/mail/verification/{id}/{token}", name="email_verify")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function verifyEmailAction(Request $request, Utilisateur $user, $token){
        if(!$user) {
            return $this->createNotFoundException("Utilisateur non trouvé");
        }
        if($user->getToken() === $token) {
            $user->setToken(null)
                ->setConfirmedAt(new \DateTime('now'));
            $this->addFlash("success", "Votre profil a bien été confirmé ! Vous pouvez désormais vous connecter");
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute("login");
        } else {
            $this->addFlash("error", "Les jetons d'accès ne correspondent pas ! Impossible de confirmer votre compte.");
            return $this->redirectToRoute("login");
        }
    }

}
