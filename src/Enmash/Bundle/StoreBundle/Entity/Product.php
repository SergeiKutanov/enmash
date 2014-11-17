<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Product
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(name="acronym", type="string", length=255)
     */
    private $acronym;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mansku", type="string", length=15, nullable=true)
     */
    private $mansku;

    /**
     * @ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="products")
     * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id")
     */
    private $manufacturer;

    /**1
     * @var StoreImage
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist"}, fetch="LAZY")
     */
    protected $productImages;

    /**
     * @ORM\OneToMany(targetEntity="ProductParameter", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $parameters;

//    /**
//     * @ORM\PrePersist
//     * @ORM\PreUpdate
//     */
//    public function restoreRelations() {
//        foreach ($this->getParameters() as $parameter) {
//            /* @var $parameter ProductParameter */
//            $parameter->setProduct($this);
//        }
//    }

    /**
     * @ORM\ManyToMany(targetEntity="Enmash\Bundle\PagesBundle\Entity\Article", mappedBy="products")
     **/
    private $articles;

    /**
     * @ORM\ManyToMany(targetEntity="Product")
     * @ORM\JoinTable(
     *      name="product_analogs",
     *      joinColumns={
     *          @ORM\JoinColumn(name="product_id", referencedColumnName="id"),
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="analog_id", referencedColumnName="id")
     *      }
     * )
     */
    private $analogs;

    /**
     * @ORM\ManyToMany(targetEntity="Product")
     * @ORM\JoinTable(
     *      name="product_similar",
     *      joinColumns={
     *          @ORM\JoinColumn(name="product_id", referencedColumnName="id"),
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="similar_product_id", referencedColumnName="id")
     *      }
     * )
     */
    private $similars;

    public function __toString() {
        if ($this->getId()) {
            return $this->getSku() . ' - ' . $this->getCategory()->getName() . ' - ' . $this->getAcronym();
        }
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
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
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
     * Get acronym
     *
     * @return string
     */
    public function getAcronym()
    {
        return $this->acronym;
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
     * Get mansku
     *
     * @return string
     */
    public function getMansku()
    {
        return $this->mansku;
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
     * Get category
     *
     * @return \Enmash\Bundle\StoreBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
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
     * Set manufacterer
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Manufacturer $manufacterer
     * @return Product
     */
    public function setManufacturer(\Enmash\Bundle\StoreBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacterer
     *
     * @return \Enmash\Bundle\StoreBundle\Entity\Manufacturer 
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set productImages
     *
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $productImages
     * @return Product
     */
    public function setProductImages(\Application\Sonata\MediaBundle\Entity\Gallery $productImages = null)
    {
        $this->productImages = $productImages;

        return $this;
    }

    /**
     * Get productImages
     *
     * @return \Application\Sonata\MediaBundle\Entity\Gallery 
     */
    public function getProductImages()
    {
        return $this->productImages;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sku = 0000;
        $this->mansku = 0000;
        $this->name = 'Название товара';
        $this->acronym = 'Сокр. назв. тов.';
        $this->parameters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parameters
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\ProductParameter $parameters
     * @return Product
     */
    public function addParameter(\Enmash\Bundle\StoreBundle\Entity\ProductParameter $parameters)
    {
        $this->parameters[] = $parameters;

        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\ProductParameter $parameters
     */
    public function removeParameter(\Enmash\Bundle\StoreBundle\Entity\ProductParameter $parameters)
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
     * Add articles
     *
     * @param \Enmash\Bundle\PagesBundle\Entity\Article $articles
     * @return Product
     */
    public function addArticle(\Enmash\Bundle\PagesBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \Enmash\Bundle\PagesBundle\Entity\Article $articles
     */
    public function removeArticle(\Enmash\Bundle\PagesBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add analogs
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $analogs
     * @return Product
     */
    public function addAnalog(\Enmash\Bundle\StoreBundle\Entity\Product $analogs)
    {
        $this->analogs[] = $analogs;

        return $this;
    }

    /**
     * Remove analogs
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $analogs
     */
    public function removeAnalog(\Enmash\Bundle\StoreBundle\Entity\Product $analogs)
    {
        $this->analogs->removeElement($analogs);
    }

    /**
     * Get analogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnalogs()
    {
        return $this->analogs;
    }

    /**
     * Add similars
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $similars
     * @return Product
     */
    public function addSimilar(\Enmash\Bundle\StoreBundle\Entity\Product $similars)
    {
        $this->similars[] = $similars;

        return $this;
    }

    /**
     * Remove similars
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $similars
     */
    public function removeSimilar(\Enmash\Bundle\StoreBundle\Entity\Product $similars)
    {
        $this->similars->removeElement($similars);
    }

    /**
     * Get similars
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSimilars()
    {
        return $this->similars;
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
}
