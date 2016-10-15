<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Utilisateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $name
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        if($this->get('security.token_storage')->getToken()->getUser() instanceof Utilisateur) {
            return $this->redirectToRoute('my_profile');
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $translated = null;
        if($error){
            switch ($error->getCode()) {
                case 0:
                    $translated = "Identifiants de connexions non reconnus";
                    break;
            }
        }
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $translated,
        ));
    }

    /**
     * @param Request $request
     * @Route("/password/recover", name="recover_password")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function recoverPasswordAction(Request $request) {

        $email = $request->get('_email_recovery');
        $user = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findOneBy(['email' => $email]);
        if($user){
            $utils = $this->get('app.utils');
            $passwordToken = $utils->str_random();
            $user->setPasswordToken($passwordToken);
            $this->getDoctrine()->getEntityManager()->flush();
            $globals = $this->get('twig')->getGlobals();
            $utils->sendMail("Changement de mot de passe", 'no-reply@' . strtolower($globals['app_name']) . '.fr', $globals['app_name'], $user->getEmail(), $this->renderView('mails/recover.html.twig', ['user' => $user, 'password_token' => $passwordToken]), 'text/html');
            $this->addFlash("notification info", "Un lien concernant la réinitialisation de votre mot de passe vous a été envoyé à votre adresse mail.");
            return $this->redirectToRoute('login');
        }
        $this->addFlash("notification error", "Cette adresse mail n'est associée à aucun compte !");
        return $this->redirectToRoute('login');
    }

    /**
     * @param Request $request
     * @Route("/password/reset/{username}/{password_token}", name="reset_password")
     */
    public function resetPasswordAction(Request $request, Utilisateur $user, $password_token){
        if($request->getMethod() === "GET"){
            if($user){
                if($user->getPasswordToken()) {
                    if($user->getPasswordToken() == $password_token) {
                        return $this->render('security/reset.html.twig');
                    }
                }
                $this->addFlash("notification error", "Erreur lors de la vérification de votre token du mot de passe !");
                return $this->redirectToRoute('homepage');
            }

            $this->addFlash("notification error", "Action non autorisée !");
            return $this->redirectToRoute('homepage');

        } else {
            $password = $request->get('_password')[0];
            if($password != $request->get('_password')[1]) {
                $this->addFlash('notification error', "Les mots de passe ne correspondent pas !");
                return $this->redirectToRoute('reset_password', ['username' => $user->getUsername(), 'password_token' => $password_token]);
            }
            $user->setPassword($this->get('security.password_encoder')->encodePassword($user, $password));
            $user->setPasswordToken(null);
            $user->setPasswordResetedAt(new \DateTime('now'));
            $this->getDoctrine()->getEntityManager()->flush();
            $this->addFlash("notification success", "Votre mot de passe a bien été modifié ! Vous pouvez désormais vous connecter.");
            return $this->redirectToRoute('login');
        }

    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request){

        if($request->getMethod() == "POST") {
            $email = $request->get('_email');
            $username = $request->get('_username');
            $password = $request->get('_password')[0];

            $userRepository = $this->getDoctrine()->getRepository('AppBundle:Utilisateur');


            $user = $userRepository->findOneBy(['username' => $username]);

            if($user){
               $this->addFlash('notification notification error', "L'utilisateur '" . $user->getUsername() . "' existe déjà !");
                return $this->redirectToRoute('login');
            }

            if($password != $request->get('_password')[1]) {
                $this->addFlash('notification error', "Les mots de passe ne correspondent pas !");
                return $this->redirectToRoute('login');
            }

            $usermail = $userRepository->findOneBy(['email' => $email]);
            if($usermail){
                $this->addFlash('notification error', "Un utilisateur avec l'email '" . $usermail->getEmail() . "' existe déjà !");
                return $this->redirectToRoute('login');
            }

            $newUser = new Utilisateur();
            $newUser->setCreatedAt(new \DateTime('now'))
                ->setEmail($email)
                ->setUsername($username)
                ->setPassword($this->get('security.password_encoder')->encodePassword($newUser, $password))
                ->addRole($this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(['role' => 'ROLE_USER']));
            $em = $this->getDoctrine()->getManager();
            $em->persist($newUser);
            $em->flush();

            return $this->redirectToRoute('email_send_verification', ['id' => $userRepository->findOneBy(['email' => $newUser->getEmail()])->getId()]);

        }

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(){
    }

}
