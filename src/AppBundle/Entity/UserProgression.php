<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProgression
 *
 * @ORM\Table(name="user_progression")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserProgressionRepository")
 */
class UserProgression
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="progression", type="integer", nullable=true)
     */
    private $progression;

    /**
     * @var array
     *
     * @ORM\Column(name="completed_pages", type="simple_array", nullable=true)
     */
    private $completedPages = [];


    /**
     * @var Tutoriel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tutoriel", inversedBy="userProgression")
     */
    private $tutoriel;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     */
    private $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set finishedAt
     *
     * @param \DateTime $finishedAt
     *
     * @return UserProgression
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * Get finishedAt
     *
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * Set progression
     *
     * @param integer $progression
     *
     * @return UserProgression
     */
    public function setProgression($progression)
    {
        $this->progression = $progression;

        return $this;
    }

    /**
     * Get progression
     *
     * @return int
     */
    public function getProgression()
    {
        return $this->progression;
    }

    /**
     * Set tutoriel
     *
     * @param \AppBundle\Entity\Tutoriel $tutoriel
     *
     * @return UserProgression
     */
    public function setTutoriel(\AppBundle\Entity\Tutoriel $tutoriel = null)
    {
        $this->tutoriel = $tutoriel;

        return $this;
    }

    /**
     * Get tutoriel
     *
     * @return \AppBundle\Entity\Tutoriel
     */
    public function getTutoriel()
    {
        return $this->tutoriel;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\Utilisateur $user
     *
     * @return UserProgression
     */
    public function setUser(\AppBundle\Entity\Utilisateur $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set completedPages
     *
     * @param array $completedPages
     *
     * @return UserProgression
     */
    public function setCompletedPages($completedPages)
    {
        $this->completedPages = $completedPages;

        return $this;
    }


    /**
     * Get completedPages
     *
     * @return array
     */
    public function getCompletedPages()
    {
        return $this->completedPages;
    }

    /**
     * @param Tutoriel $tutoriel
     * @return bool
     */
    public function hasCompletedTutoriel(Tutoriel $tutoriel){
        return (count($this->getCompletedPages()) == $tutoriel->getTutorialPages()->count());
    }

    /**
     * @return bool
     */
    public function hasStartedTutoriel(){
        return (count($this->getCompletedPages()) > 0);
    }

    /**
     * @return int
     */
    public function getLastCompletedPageNumber(){
        return max($this->getCompletedPages());
    }
}
