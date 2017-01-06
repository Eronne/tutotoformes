<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UtilisateurRepository")
 */
class Utilisateur implements UserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

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
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $last_login;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=60, nullable=true)
     */
    private $token;

    /**
     * @var \DateTime
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     */
    private $confirmed_at;

    /**
     * @var string
     * @ORM\Column(name="password_token", type="string", length=60, nullable=true)
     */
    private $passwordToken;

    /**
     * @var string
     * @ORM\Column(name="password_reseted_at", type="datetime", nullable=true)
     */
    private $passwordResetedAt;

    /**
     * @var Role[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tutoriel", mappedBy="authors")
     */
    private $tutoriels;

    /**
     * @var UtilisateurAchievementAssociation
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UtilisateurAchievementAssociation", mappedBy="utilisateur", cascade={"remove"})
     */
    private $userAchievementsAssociation;

    /**
     * @var Comment[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="author", cascade={"remove"})
     */
    private $comments;





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
     * Set username
     *
     * @param string $username
     *
     * @return Utilisateur
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Utilisateur
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Utilisateur
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set editedAt
     *
     * @param \DateTime $editedAt
     *
     * @return Utilisateur
     */
    public function setEditedAt($editedAt)
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    /**
     * Get editedAt
     *
     * @return \DateTime
     */
    public function getEditedAt()
    {
        return $this->editedAt;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Utilisateur
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));

    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);

    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->roles as $role){
            array_push($roles, $role->getRole());
        }


        return array_unique($roles);
    }

    public function isExplicitlyGranted($role){
        return in_array($role, $this->getRoles());
    }

    public function getRolesObject(){
        return $this->roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add role
     *
     * @param \AppBundle\Entity\Role $role
     *
     * @return Utilisateur
     */
    public function addRole(\AppBundle\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \AppBundle\Entity\Role $role
     */
    public function removeRole(\AppBundle\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }




    /**
     * Add tutoriel
     *
     * @param \AppBundle\Entity\Tutoriel $tutoriel
     *
     * @return Utilisateur
     */
    public function addTutoriel(\AppBundle\Entity\Tutoriel $tutoriel)
    {
        $this->tutoriels[] = $tutoriel;

        return $this;
    }

    /**
     * Remove tutoriel
     *
     * @param \AppBundle\Entity\Tutoriel $tutoriel
     */
    public function removeTutoriel(\AppBundle\Entity\Tutoriel $tutoriel)
    {
        $this->tutoriels->removeElement($tutoriel);
    }

    /**
     * Get tutoriels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTutoriels()
    {
        return $this->tutoriels;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Utilisateur
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set confirmedAt
     *
     * @param \DateTime $confirmedAt
     *
     * @return Utilisateur
     */
    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmed_at = $confirmedAt;

        return $this;
    }

    /**
     * Get confirmedAt
     *
     * @return \DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmed_at;
    }

    public function isConfirmed() {
        return ($this->getConfirmedAt() != null);
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return Utilisateur
     */
    public function setLastLogin($lastLogin)
    {
        $this->last_login = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * Set passwordToken
     *
     * @param string $passwordToken
     *
     * @return Utilisateur
     */
    public function setPasswordToken($passwordToken)
    {
        $this->passwordToken = $passwordToken;

        return $this;
    }

    /**
     * Get passwordToken
     *
     * @return string
     */
    public function getPasswordToken()
    {
        return $this->passwordToken;
    }

    /**
     * Set passwordResetedAt
     *
     * @param \DateTime $passwordResetedAt
     *
     * @return Utilisateur
     */
    public function setPasswordResetedAt($passwordResetedAt)
    {
        $this->passwordResetedAt = $passwordResetedAt;

        return $this;
    }

    /**
     * Get passwordResetedAt
     *
     * @return \DateTime
     */
    public function getPasswordResetedAt()
    {
        return $this->passwordResetedAt;
    }

    /**
     * Add userAchievementsAssociation
     *
     * @param \AppBundle\Entity\UtilisateurAchievementAssociation $userAchievementsAssociation
     *
     * @return Utilisateur
     */
    public function addUserAchievementsAssociation(\AppBundle\Entity\UtilisateurAchievementAssociation $userAchievementsAssociation)
    {
        $this->userAchievementsAssociation[] = $userAchievementsAssociation;

        return $this;
    }

    /**
     * Remove userAchievementsAssociation
     *
     * @param \AppBundle\Entity\UtilisateurAchievementAssociation $userAchievementsAssociation
     */
    public function removeUserAchievementsAssociation(\AppBundle\Entity\UtilisateurAchievementAssociation $userAchievementsAssociation)
    {
        $this->userAchievementsAssociation->removeElement($userAchievementsAssociation);
    }

    /**
     * Get userAchievementsAssociation
     *
     * @return UtilisateurAchievementAssociation[]
     */
    public function getUserAchievementsAssociation()
    {
        return $this->userAchievementsAssociation;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Utilisateur
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
