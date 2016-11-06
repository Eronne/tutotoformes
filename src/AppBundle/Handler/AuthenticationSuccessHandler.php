<?php
namespace AppBundle\Handler;

use AppBundle\Entity\Achievement;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurAchievementAssociation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    protected $container;

    public function __construct(HttpUtils $httpUtils, \Symfony\Component\DependencyInjection\ContainerInterface $cont, array $options)
    {
        parent::__construct($httpUtils, $options);
        $this->container = $cont;
    }

    public function onAuthenticationSuccess(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token)
    {
        /** @var Utilisateur $user */
        $user = $token->getUser();
        $user->setLastLogin(new \DateTime("now"));

        $em = $this->container->get('doctrine.orm.entity_manager');

        $em->persist($user);
        $em->flush();

        if (!$user->isConfirmed()) {

            $this->container->get('session')->getFlashBag()->add('alert info', "Votre compte n'est pas encore validé. Veuillez vérifier vos mails. 
            Si vous n'avez rien reçu, cliquez sur <a href='" . $this->container->get('router')->generate("email_send_verification", ['id' => $user->getId()]) . "'>ce lien</a> pour renvoyer un mail.");
            $this->container->get('security.token_storage')->setToken(null);
            return $this->httpUtils->createRedirectResponse($request, "/login");
        }

        /** @var Achievement $achievement */
        $achievement = $em->getRepository('AppBundle:Achievement')->findOneBy(['internalName' => 'FIRST_LOGIN']);
        if(!$em->getRepository('AppBundle:UtilisateurAchievementAssociation')->haveUserUnlocked($user, $achievement)) {
            $userAchievement = new UtilisateurAchievementAssociation();
            $userAchievement->setAchievement($achievement)
                ->setUtilisateur($user)
                ->setUnlockedAt(new \DateTime('now'));
            $em->persist($userAchievement);
            $em->flush();
            $this->container->get('session')->getFlashBag()->add('alert success', "Tu as obtenu le succès '" . $achievement->getTitle() . "'");
        }

        return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
    }
}