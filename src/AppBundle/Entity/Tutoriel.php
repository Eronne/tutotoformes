<?php

namespace AppBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur", inversedBy="tutoriels")
     */
    private $author;




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
     * @return  \Doctrine\Common\Collections\Collection|TutorielPage[]
     */
    public function getTutorialPages()
    {
        return $this->tutorialPages;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\Utilisateur $author
     *
     * @return Tutoriel
     */
    public function setAuthor(\AppBundle\Entity\Utilisateur $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getAuthor()
    {
        return $this->author;
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
    public function setImageFile(File $image = null)
    {
        $this->thumbnailFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->editedAt = new \DateTime('now');
        }

        return $this;
    }
}
