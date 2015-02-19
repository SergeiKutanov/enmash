<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ShoppingCart
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ShoppingCart
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
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", nullable=true)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Enmash\Bundle\UserControlBundle\Entity\User", inversedBy="shoppingCartItems")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


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
     * Set quantity
     *
     * @param integer $quantity
     * @return ShoppingCart
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return ShoppingCart
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    /**
     * Set product
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Product $product
     * @return ShoppingCart
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
     * Set user
     *
     * @param \Enmash\Bundle\UserControlBundle\Entity\User $user
     * @return ShoppingCart
     */
    public function setUser(\Enmash\Bundle\UserControlBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Enmash\Bundle\UserControlBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return ShoppingCart
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
