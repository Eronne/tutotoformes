<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tutoriel
 *
 * @ORM\Table(name="tutoriel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TutorielRepository")
 */
class Tutoriel
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var int
     *
     * @ORM\Column(name="difficulty", type="integer")
     */
    private $difficulty;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="edited_at", type="datetime", nullable=true)
     */
    private $editedAt;

    /**
     * @var TutorielPage[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TutorielPage", mappedBy="tutoriel")
     */
    private $tutorialPages;

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
     * Set title
     *
     * @param string $title
     *
     * @return Tutoriel
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     *
     * @return Tutoriel
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Tutoriel
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set difficulty
     *
     * @param integer $difficulty
     *
     * @return Tutoriel
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Get difficulty
     *
     * @return int
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * @param \DateTime $editedAt
     * @return Tutoriel
     */
    public function setEditedAt($editedAt)
    {
        $this->editedAt = $editedAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEditedAt()
    {
        return $this->editedAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Tutoriel
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tutorialPages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tutorialPage
     *
     * @param \AppBundle\Entity\TutorielPage $tutorialPage
     *
     * @return Tutoriel
     */
    public function addTutorialPage(\AppBundle\Entity\TutorielPage $tutorialPage)
    {
        $this->tutorialPages[] = $tutorialPage;

        return $this;
    }

    /**
     * Remove tutorialPage
     *
     * @param \AppBundle\Entity\TutorielPage $tutorialPage
     */
    public function removeTutorialPage(\AppBundle\Entity\TutorielPage $tutorialPage)
    {
        $this->tutorialPages->removeElement($tutorialPage);
    }

    /**
     * Get tutorialPages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTutorialPages()
    {
        return $this->tutorialPages;
    }
}
