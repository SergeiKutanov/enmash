<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Enmash\Bundle\StoreBundle\Entity\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @OneToMany(targetEntity="Product", mappedBy="category", cascade={"persist", "remove"}, )
     */
    private $products;

    /**
     * @OneToMany(targetEntity="Category", mappedBy="parentCategory", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $subCategories;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="subCategories", cascade={"all"})
     * @ORM\JoinColumn(name="parent_category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentCategory;

    /**
     * @ORM\ManyToMany(targetEntity="CategoryParameter")
     * @ORM\JoinTable(
     *      name="category_category_parameters",
     *      joinColumns={
     *          @ORM\JoinColumn(name="category_id", referencedColumnName="id"),
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="parameter_id", referencedColumnName="id")
     *      }
     * )
     */
    private $parameters;


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
     * @return Category
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
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add products
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $products
     * @return Category
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

    public function __toString() {
        return $this->getName();
    }

    /**
     * Add subCategories
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Category $subCategories
     * @return Category
     */
    public function addSubCategory(\Enmash\Bundle\StoreBundle\Entity\Category $subCategories)
    {
        $this->subCategories[] = $subCategories;

        return $this;
    }

    /**
     * Remove subCategories
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Category $subCategories
     */
    public function removeSubCategory(\Enmash\Bundle\StoreBundle\Entity\Category $subCategories)
    {
        $this->subCategories->removeElement($subCategories);
    }

    /**
     * Get subCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubCategories()
    {
        return $this->subCategories;
    }

    /**
     * Set parentCategory
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Category $parentCategory
     * @return Category
     */
    public function setParentCategory(\Enmash\Bundle\StoreBundle\Entity\Category $parentCategory = null)
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    /**
     * Get parentCategory
     *
     * @return \Enmash\Bundle\StoreBundle\Entity\Category 
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    /**
     * Add parameters
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\CategoryParameter $parameters
     * @return Category
     */
    public function addParameter(\Enmash\Bundle\StoreBundle\Entity\CategoryParameter $parameters)
    {
        $this->parameters[] = $parameters;

        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\CategoryParameter $parameters
     */
    public function removeParameter(\Enmash\Bundle\StoreBundle\Entity\CategoryParameter $parameters)
    {
        $this->parameters->removeElement($parameters);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Category
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
}
