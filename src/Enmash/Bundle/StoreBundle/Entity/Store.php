<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Store
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Enmash\Bundle\StoreBundle\Entity\StoreRepository")
 */
class Store
{
    const RETAIL_TYPE = 1;
    const WHOLESALE_TYPE = 2;
    const BOTH_TYPE = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule", type="string", length=255)
     */
    private $schedule;

    /**
     * @var boolean
     * @ORM\Column(name="publish", type="boolean")
     */
    private $publish;


    /**
     * @var StoreImage
     * @ORM\OneToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist"}, fetch="LAZY")
     */
    protected $storeImages;

//    /**
//     * @ORM\OneToMany(targetEntity="StoreImage", mappedBy="store")
//     */
//    private $storeImages;

    /**
     * @var string
     * @ORM\Column(name="info", type="text")
     */
    protected $info;

    /**
     * @var int
     * @ORM\Column(name="store_type", type="integer")
     */
    protected $storeType;

    /**
     * @ORM\OneToMany(targetEntity="StoreContact", mappedBy="store", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $contacts;

    public static function getstoreTypeList() {
        return array(
            self::RETAIL_TYPE       => self::getStoreTypeString(self::RETAIL_TYPE),
            self::WHOLESALE_TYPE    => self::getStoreTypeString(self::WHOLESALE_TYPE),
            self::BOTH_TYPE         => self::getStoreTypeString(self::BOTH_TYPE)
        );
    }

    public static function getStoreTypeString($type) {
        switch ($type) {
            case self::RETAIL_TYPE : return 'Розничный магазин';
            case self::WHOLESALE_TYPE : return 'Служба сбыта';
            case self::BOTH_TYPE: return 'Розничный магазин и служба сбыта';
        }
    }

    public function __toString() {
        return $this->getName() . ' - ' . $this->getAddress();
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
     * @return Store
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
     * Set address
     *
     * @param string $address
     * @return Store
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Store
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Store
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->publish = false;
//        $this->storeImages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->storeType = self::BOTH_TYPE;
    }


    /**
     * Set storeImages
     *
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $storeImages
     * @return Store
     */
    public function setStoreImages(\Application\Sonata\MediaBundle\Entity\Gallery $storeImages = null)
    {
        $this->storeImages = $storeImages;

        return $this;
    }

    /**
     * Get storeImages
     *
     * @return \Application\Sonata\MediaBundle\Entity\Gallery 
     */
    public function getStoreImages()
    {
        return $this->storeImages;
    }

    /**
     * Set schedule
     *
     * @param string $schedule
     * @return Store
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return string 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set publish
     *
     * @param boolean $publish
     * @return Store
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return boolean 
     */
    public function getPublish()
    {
        return $this->publish;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return Store
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set storeType
     *
     * @param integer $storeType
     * @return Store
     */
    public function setStoreType($storeType)
    {
        $this->storeType = $storeType;

        return $this;
    }

    /**
     * Get storeType
     *
     * @return integer 
     */
    public function getStoreType()
    {
        return $this->storeType;
    }

    /**
     * Add contacts
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\StoreContact $contacts
     * @return Store
     */
    public function addContact(\Enmash\Bundle\StoreBundle\Entity\StoreContact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\StoreContact $contacts
     */
    public function removeContact(\Enmash\Bundle\StoreBundle\Entity\StoreContact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }
}
