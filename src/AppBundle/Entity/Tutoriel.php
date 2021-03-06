<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Tutoriel
 *
 * @ORM\Table(name="tutoriel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TutorielRepository")
 * @Vich\Uploadable()
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
     * @var string
     * @ORM\Column(name="thumbnail_link", type="string", length=255, nullable=true)
     */
    private $thumbnailLink;

    /**
     * @var File
     * @Vich\UploadableField(mapping="tutoriel_thumb", fileNameProperty="thumbnailLink")
     */
    private $thumbnailFile;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var int
     *
     * @ORM\Column(name="difficulty", type="string", length=60)
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
     * @var bool
     * @ORM\Column(name="is_draft", type="boolean", nullable=false)
     */
    private $isDraft = false;

    /**
     * @var string
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var TutorielPage[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TutorielPage", mappedBy="tutoriel", cascade={"remove"})
     */
    private $tutorialPages;

    /**
     * @var Utilisateur[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Utilisateur", inversedBy="tutoriels")
     */
    private $authors;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserProgression", mappedBy="tutoriel", cascade={"remove"})
     */
    private $userProgression;




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
        $this->slug = self::slugify($title);

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
     * @param string $difficulty
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
     * @return string
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
        $this->userProgression = new ArrayCollection();
    }

    /**
     * Add tutorialPage
     *
     * @param \AppBundle\Entity\TutorielPage $tutorialPage
     *
     * @return Tutoriel
     */
    public function addTutorialPage(TutorielPage $tutorialPage)
    {
        $this->tutorialPages[] = $tutorialPage;

        return $this;
    }

    /**
     * Remove tutorialPage
     *
     * @param \AppBundle\Entity\TutorielPage $tutorialPage
     */
    public function removeTutorialPage(TutorielPage $tutorialPage)
    {
        $this->tutorialPages->removeElement($tutorialPage);
    }

    /**
     * Get tutorialPages
     *
     * @param string $order
     * @return TutorielPage[]|\Doctrine\Common\Collections\Collection
     */
    public function getTutorialPages($order = 'ASC')
    {
        $criteria = Criteria::create()->orderBy(['pageNumber' => $order]);
        return $this->tutorialPages->matching($criteria);
    }




    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return Tutoriel
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Tutoriel
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    static private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function hasFinish(Utilisateur $user){
        return ($this->getUserProgression($user)->getProgression() == 100);
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tutoriel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set thumbnailLink
     *
     * @param string $thumbnailLink
     *
     * @return Tutoriel
     */
    public function setThumbnailLink($thumbnailLink)
    {
        $this->thumbnailLink = $thumbnailLink;

        return $this;
    }

    /**
     * Get thumbnailLink
     *
     * @return string
     */
    public function getThumbnailLink()
    {
        return $this->thumbnailLink;
    }

    /**
     * @return File
     */
    public function getThumbnailFile()
    {
        return $this->thumbnailFile;
    }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Tutoriel
     */
    public function setThumbnailFile(File $image = null)
    {
        $this->thumbnailFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->editedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Add userProgression
     *
     * @param \AppBundle\Entity\UserProgression $userProgression
     *
     * @return Tutoriel
     */
    public function addUserProgression(\AppBundle\Entity\UserProgression $userProgression)
    {
        $this->userProgression[] = $userProgression;

        return $this;
    }

    /**
     * Remove userProgression
     *
     * @param \AppBundle\Entity\UserProgression $userProgression
     */
    public function removeUserProgression(\AppBundle\Entity\UserProgression $userProgression)
    {
        $this->userProgression->removeElement($userProgression);
    }

    /**
     * Get userProgression
     *
     * @return UserProgression
     */
    public function getUserProgression($user)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('user', $user));

        return $this->userProgression->matching($criteria)->first();
    }



    /**
     * Add author
     *
     * @param \AppBundle\Entity\Utilisateur $author
     *
     * @return Tutoriel
     */
    public function addAuthor(\AppBundle\Entity\Utilisateur $author)
    {
        $this->authors[] = $author;

        return $this;
    }

    /**
     * Remove author
     *
     * @param \AppBundle\Entity\Utilisateur $author
     */
    public function removeAuthor(\AppBundle\Entity\Utilisateur $author)
    {
        $this->authors->removeElement($author);
    }

    /**
     * Get authors
     *
     * @return Utilisateur[]|\Doctrine\Common\Collections\Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
