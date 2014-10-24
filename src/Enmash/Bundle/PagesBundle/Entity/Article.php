<?php

namespace Enmash\Bundle\PagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Article
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="abstract", type="text")
     */
    private $abstract;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var boolean
     *
     * @ORM\Column(name="publish", type="boolean", nullable=true)
     */
    private $publish;

    /**
     * @var boolean
     *
     * @ORM\Column(name="featured", type="boolean", nullable=true)
     */
    private $featured;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="Enmash\Bundle\StoreBundle\Entity\Product", inversedBy="articles")
     * @ORM\JoinTable(name="article_products")
     **/
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="parentArticle", cascade={"persist", "remove"}, orphanRemoval=false)
     */
    private $connectedArticles;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="connectedArticles")
     * @ORM\JoinColumn(name="parent_article_id", referencedColumnName="id")
     */
    private $parentArticle;

    public function __toString() {
        return $this->title;
    }

    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->featured = false;
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
     * Set abstract
     *
     * @param string $abstract
     * @return Article
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * Get abstract
     *
     * @return string 
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Article
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set publish
     *
     * @param boolean $publish
     * @return Article
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Article
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
     * Add products
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $products
     * @return Article
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

    /**
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * Set slug
     *
     * @param string $slug
     * @return Article
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

    /**
     * Add connectedArticles
     *
     * @param \Enmash\Bundle\PagesBundle\Entity\Article $connectedArticles
     * @return Article
     */
    public function addConnectedArticle(\Enmash\Bundle\PagesBundle\Entity\Article $connectedArticles)
    {
        $this->connectedArticles[] = $connectedArticles;

        return $this;
    }

    /**
     * Remove connectedArticles
     *
     * @param \Enmash\Bundle\PagesBundle\Entity\Article $connectedArticles
     */
    public function removeConnectedArticle(\Enmash\Bundle\PagesBundle\Entity\Article $connectedArticles)
    {
        $this->connectedArticles->removeElement($connectedArticles);
    }

    /**
     * Get connectedArticles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConnectedArticles()
    {
        return $this->connectedArticles;
    }

    /**
     * Set parentArticle
     *
     * @param \Enmash\Bundle\PagesBundle\Entity\Article $parentArticle
     * @return Article
     */
    public function setParentArticle(\Enmash\Bundle\PagesBundle\Entity\Article $parentArticle = null)
    {
        $this->parentArticle = $parentArticle;

        return $this;
    }

    /**
     * Get parentArticle
     *
     * @return \Enmash\Bundle\PagesBundle\Entity\Article 
     */
    public function getParentArticle()
    {
        return $this->parentArticle;
    }

    /**
     * Set featured
     *
     * @param boolean $featured
     * @return Article
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * Get featured
     *
     * @return boolean 
     */
    public function getFeatured()
    {
        return $this->featured;
    }
}
