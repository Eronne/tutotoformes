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
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
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
               $this->addFlash('negative', "L'utilisateur '" . $user->getUsername() . "' existe déjà !");
                return $this->redirectToRoute('login');
            }

            if($password != $request->get('_password')[1]) {
                $this->addFlash('negative', "Les mots de passe ne correspondent pas !");
                return $this->redirectToRoute('login');
            }

            $usermail = $userRepository->findOneBy(['email' => $email]);
            if($usermail){
                $this->addFlash('negative', "Un utilisateur avec l'email '" . $usermail->getEmail() . "' existe déjà !");
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
            $this->addFlash('positive', "Vous avez bien été inscrit ! Vous pouvez désormais vous connecter.");
            return $this->redirectToRoute('login');

        }

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(){

    }

}
