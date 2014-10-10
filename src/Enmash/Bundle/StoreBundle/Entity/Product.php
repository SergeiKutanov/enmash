<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Product
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Enmash\Bundle\StoreBundle\Entity\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=15)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="acronym", type="string", length=255)
     */
    private $acronym;

    /**
     * @var string
     *
     * @ORM\Column(name="mansku", type="string", length=15)
     */
    private $mansku;

    /**
     * @ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;


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
     * Set sku
     *
     * @param string $sku
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
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
     * Set acronym
     *
     * @param string $acronym
     * @return Product
     */
    public function setAcronym($acronym)
    {
        $this->acronym = $acronym;

        return $this;
    }

    /**
     * Get acronym
     *
     * @return string 
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * Set mansku
     *
     * @param string $mansku
     * @return Product
     */
    public function setMansku($mansku)
    {
        $this->mansku = $mansku;

        return $this;
    }

    /**
     * Get mansku
     *
     * @return string 
     */
    public function getMansku()
    {
        return $this->mansku;
    }

    /**
     * Set category
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Category $category
     * @return Product
     */
    public function setCategory(\Enmash\Bundle\StoreBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Enmash\Bundle\StoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
