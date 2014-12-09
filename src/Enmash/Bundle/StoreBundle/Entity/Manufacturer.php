<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Manufacturer
 *
 * @ORM\Table()
 * @ORM\Entity
 *
 * @ExclusionPolicy("all")
 */
class Manufacturer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showWebSite", type="boolean", nullable=true)
     */
    private $showWebSite;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="manufacturer", cascade={"persist", "remove"})
     */
    private $products;

    public function __toString () {
        return $this->getName();
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
     * @return Manufacturer
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
     * Set website
     *
     * @param string $website
     * @return Manufacturer
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set showWebSite
     *
     * @param boolean $showWebSite
     * @return Manufacturer
     */
    public function setShowWebSite($showWebSite)
    {
        $this->showWebSite = $showWebSite;

        return $this;
    }

    /**
     * Get showWebSite
     *
     * @return boolean 
     */
    public function getShowWebSite()
    {
        return $this->showWebSite;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->showWebSite = false;
    }

    /**
     * Add products
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $products
     * @return Manufacturer
     */
    public function addProduct(\Enmash\Bundle\StoreBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $products
     */
    public function removeProduct(\Enmash\Bundle\StoreBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }
}
