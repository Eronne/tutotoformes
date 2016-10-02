<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TutorielPage
 *
 * @ORM\Table(name="tutoriel_page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TutorielPageRepository")
 */
class TutorielPage
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

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
     * @var int
     * @ORM\Column(name="page_number", type="integer", nullable=false)
     */
    private $pageNumber;

    /**
     * @var string
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var Tutoriel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tutoriel", inversedBy="tutorialPages")
     */
    private $tutoriel;


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
     * @return TutorielPage
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
     * Set content
     *
     * @param string $content
     *
     * @return TutorielPage
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return TutorielPage
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
     * @return TutorielPage
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
     * Set tutoriel
     *
     * @param \AppBundle\Entity\Tutoriel $tutoriel
     *
     * @return TutorielPage
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
     * Set slug
     *
     * @param string $slug
     *
     * @return TutorielPage
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
     * Set pageNumber
     *
     * @param integer $pageNumber
     *
     * @return TutorielPage
     */
    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    /**
     * Get pageNumber
     *
     * @return integer
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @return bool|\string[]
     */
    public function getSubparts(){
        $content = $this->content;
        if($content === '') {
            return null;
        }
        $doc = new \DOMDocument();
        $doc->loadHTML($content);
        $doc->saveHTML();
        $h2 = $doc->getElementsByTagName("h2");

        $subparts = [];
        for($i = 0; $i < $h2->length; $i++) {
            array_push($subparts, ['title' => $h2->item($i)->nodeValue]);

            if($h2->item($i)->childNodes->length > 1){
                $subparts[$i]['anchor_name'] = $h2->item($i)->firstChild->attributes->getNamedItem('id')->nodeValue;
            }
        }
        return $subparts;
    }


}
