<?php

namespace AppBundle;

use AppBundle\Entity\User;
use AppBundle\Entity\Utilisateur;
use Closure;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RoleListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * YourRoleListener constructor
     *
     * @param EntityManager $entityManager The entity manager to use to load roles
     * @param TokenStorage $tokenStorage The token storage for the user
     */
    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Handles the kernel event
     *
     * @param FilterControllerEvent $event The dispatched event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($this->tokenStorage && $this->tokenStorage->getToken()) {
            $token = $this->tokenStorage->getToken();
            $user = $token->getUser();
            // This check can be just `is_object` like in symfony core
            // we're explicit about the class used
            if ($user instanceof Utilisateur) {
                // there didn't seem to be an easier way to grab the provider key,
                // so using bound closure to retrieve it
                $providerKeyGetter = function(TokenInterface $token) {
                    return $token->providerKey;
                };
                $boundProviderKeyGetter = Closure::bind($providerKeyGetter, null, $token);
                
                // check & load roles for user here if necessary
                $this->tokenStorage->setToken(
                    new UsernamePasswordToken(
                        $user,
                        $token->getCredentials(),
                        $boundProviderKeyGetter($token),
                        $user->getRoles()
                    )
                );
            }
        }

    }

    /** {@inheritdoc} */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER => ['onKernelController', 99]];
    }
}