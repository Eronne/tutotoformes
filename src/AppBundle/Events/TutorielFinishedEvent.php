<?php
/**
 * Created by IntelliJ IDEA.
 * User: Omar
 * Date: 06/11/2016
 * Time: 04:02
 */

namespace AppBundle\Events;


use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\UserProgression;
use AppBundle\Entity\Utilisateur;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;


class TutorielFinishedEvent extends Event {

    private $_tutoriel;
    private $_progression;
    private $_container;

    public function __construct(ContainerInterface $container, Tutoriel $tutoriel, UserProgression $progression)
    {
        $this->_tutoriel = $tutoriel;
        $this->_progression = $progression;
        $this->_container = $container;
    }

    /**
     * @return Tutoriel
     */
    public function getTutoriel()
    {
        return $this->_tutoriel;
    }

    /**
     * @return UserProgression
     */
    public function getProgression()
    {
        return $this->_progression;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->_container;
    }

}