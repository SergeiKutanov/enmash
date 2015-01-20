<?php

namespace Enmash\Bundle\PagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Model\Gallery;
use Sonata\MediaBundle\Model\MediaInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Banner
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Banner
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean", nullable=true)
     */
    private $isPublished = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    protected $photo;

    /**
     * @var Gallery
     * @ORM\OneToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist"}, fetch="LAZY")
     */
    protected $additionalInfoFiles;

    public function __construct() {
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
        $this->endDate->add(
            new \DateInterval('P1M')
        );
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Banner
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Banner
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
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return Banner
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Banner
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Banner
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param MediaInterface $media
     */
    public function setPhoto(MediaInterface $media)
    {
        $this->photo = $media;
    }

    /**
     * @return MediaInterface
     */
    public function getPhoto()
    {
        return $this->photo;
    }


    /**
     * Set additionalInfoFile
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $additionalInfoFile
     * @return Banner
     */
    public function setAdditionalInfoFile(\Application\Sonata\MediaBundle\Entity\Media $additionalInfoFile = null)
    {
        $this->additionalInfoFile = $additionalInfoFile;

        return $this;
    }

    /**
     * Get additionalInfoFile
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getAdditionalInfoFile()
    {
        return $this->additionalInfoFile;
    }

    /**
     * Set additionalInfoFiles
     *
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $additionalInfoFiles
     * @return Banner
     */
    public function setAdditionalInfoFiles(\Application\Sonata\MediaBundle\Entity\Gallery $additionalInfoFiles = null)
    {
        $this->additionalInfoFiles = $additionalInfoFiles;

        return $this;
    }

    /**
     * Get additionalInfoFiles
     *
     * @return \Application\Sonata\MediaBundle\Entity\Gallery 
     */
    public function getAdditionalInfoFiles()
    {
        return $this->additionalInfoFiles;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Banner
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
}
