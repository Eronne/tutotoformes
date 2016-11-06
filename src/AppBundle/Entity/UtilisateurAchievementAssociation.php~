<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateurAchievementAssociation
 *
 * @ORM\Table(name="utilisateur_achievement_association")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UtilisateurAchievementAssociationRepository")
 */
class UtilisateurAchievementAssociation
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
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur", inversedBy="userAchievementsAssociation")
     */
    private $utilisateur;

    /**
     * @var Achievement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Achievement", inversedBy="userAchievementsAssociation")
     */
    private $achievement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="unlocked_at", type="datetime", nullable=true)
     */
    private $unlockedAt;


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
     * Set unlockedAt
     *
     * @param \DateTime $unlockedAt
     *
     * @return UtilisateurAchievementAssociation
     */
    public function setUnlockedAt($unlockedAt)
    {
        $this->unlockedAt = $unlockedAt;

        return $this;
    }

    /**
     * Get unlockedAt
     *
     * @return \DateTime
     */
    public function getUnlockedAt()
    {
        return $this->unlockedAt;
    }

    /**
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return UtilisateurAchievementAssociation
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set achievement
     *
     * @param \AppBundle\Entity\Achievement $achievement
     *
     * @return UtilisateurAchievementAssociation
     */
    public function setAchievement(\AppBundle\Entity\Achievement $achievement = null)
    {
        $this->achievement = $achievement;

        return $this;
    }

    /**
     * Get achievement
     *
     * @return \AppBundle\Entity\Achievement
     */
    public function getAchievement()
    {
        return $this->achievement;
    }
}
