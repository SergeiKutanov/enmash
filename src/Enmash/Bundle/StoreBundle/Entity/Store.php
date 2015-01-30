<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
    const ORDER_TYPE = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
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
     * @ORM\Column(name="whname", type="string", length=255, nullable=true)
     */
    private $whName;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="whaddress", type="string", length=255, nullable=true)
     */
    private $whAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string")
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string")
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule", type="string", length=255)
     */
    private $schedule;

    /**
     * @var string
     *
     * @ORM\Column(name="whschedule", type="string", length=255, nullable=true)
     */
    private $wholesaleSchedule;

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
     * @var string
     * @ORM\Column(name="wh_info", type="text", nullable=true)
     */
    protected $whInfo;

    /**
     * @var int
     * @ORM\Column(name="store_type", type="json_array")
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
            self::ORDER_TYPE         => self::getStoreTypeString(self::ORDER_TYPE)
        );
    }

    public static function getStoreTypeString($type, $noRetail = false) {

        if (is_array($type)) {
            $result = '';
            if ($noRetail) {
                foreach($type as $index => $subType) {
                    $newType = array();
                    if ($subType != self::RETAIL_TYPE) {
                        $newType[] = $subType;
                    }
                }
                $type = $newType;
            }
            foreach ($type as $index => $subType) {
                if ($index > 0) $result .= ' - ';
                $result .= self::getStoreTypeString($subType);
            }
            return $result;
        }

        switch ($type) {
            case self::RETAIL_TYPE : return 'Розничный магазин';
            case self::WHOLESALE_TYPE : return 'Служба сбыта';
            case self::ORDER_TYPE: return 'Стол заказов';
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
     * Constructor
     */
    public function __construct()
    {
        $this->publish = false;
//        $this->storeImages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->storeType = array(self::RETAIL_TYPE);
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


    /**
     * Set storeType
     *
     * @param array $storeType
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
     * @return array 
     */
    public function getStoreType()
    {
        return $this->storeType;
    }

    /**
     * Set whInfo
     *
     * @param string $whInfo
     * @return Store
     */
    public function setWhInfo($whInfo)
    {
        $this->whInfo = $whInfo;

        return $this;
    }

    /**
     * Get whInfo
     *
     * @return string 
     */
    public function getWhInfo()
    {
        return $this->whInfo;
    }

    public function getAddressWithoutCity() {
        return preg_replace('/^([^,]+),\s/', '', $this->getAddress());
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Store
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    public function getTypesString() {
        $typesString = '';
        $trailingComaOffset = count($this->getStoreType()) - 1;
        foreach ($this->getStoreType() as $index => $type) {
            $typesString .= $this->getStoreTypeString($type);
            if ($index != $trailingComaOffset) {
                $typesString .= ' | ';
            }
        }
        return $typesString;
    }

    /**
     * Set wholesaleSchedule
     *
     * @param string $wholesaleSchedule
     * @return Store
     */
    public function setWholesaleSchedule($wholesaleSchedule)
    {
        $this->wholesaleSchedule = $wholesaleSchedule;

        return $this;
    }

    /**
     * Get wholesaleSchedule
     *
     * @return string 
     */
    public function getWholesaleSchedule()
    {
        return $this->wholesaleSchedule;
    }

    /**
     * Set whName
     *
     * @param string $whName
     * @return Store
     */
    public function setWhName($whName)
    {
        $this->whName = $whName;

        return $this;
    }

    /**
     * Get whName
     *
     * @return string 
     */
    public function getWhName()
    {
        return $this->whName;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Store
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

    /**
     * Set whAddress
     *
     * @param string $whAddress
     * @return Store
     */
    public function setWhAddress($whAddress)
    {
        $this->whAddress = $whAddress;

        return $this;
    }

    /**
     * Get whAddress
     *
     * @return string 
     */
    public function getWhAddress()
    {
        return $this->whAddress;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
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
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
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
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
