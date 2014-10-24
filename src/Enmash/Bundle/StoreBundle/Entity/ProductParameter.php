<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductParameter
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductParameter
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
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="parameters")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="CategoryParameter")
     * @ORM\JoinColumn(name="category_parameter_id", referencedColumnName="id")
     */
    private $categoryParameter;


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
     * Set value
     *
     * @param string $value
     * @return ProductParameter
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set product
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $product
     * @return ProductParameter
     */
    public function setProduct(\Enmash\Bundle\StoreBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Enmash\Bundle\StoreBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set categoryParameter
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\CategoryParameter $categoryParameter
     * @return ProductParameter
     */
    public function setCategoryParameter(\Enmash\Bundle\StoreBundle\Entity\CategoryParameter $categoryParameter = null)
    {
        $this->categoryParameter = $categoryParameter;

        return $this;
    }

    /**
     * Get categoryParameter
     *
     * @return \Enmash\Bundle\StoreBundle\Entity\CategoryParameter 
     */
    public function getCategoryParameter()
    {
        return $this->categoryParameter;
    }
}
