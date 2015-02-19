<?php

namespace Enmash\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PaymentOrder
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PaymentOrder
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
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean")
     */
    private $confirmed = false;

    /**
     * @ORM\ManyToOne(targetEntity="Enmash\Bundle\UserControlBundle\Entity\User", inversedBy="paymentOrders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="ProductOrder", mappedBy="order", cascade={"all"}, orphanRemoval=true)
     */
    private $products;

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
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return ProductOrder
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return ProductOrder
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

    /**
     * Set user
     *
     * @param \Enmash\Bundle\UserControlBundle\Entity\User $user
     * @return ProductOrder
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
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add products
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\ProductOrder $products
     * @return PaymentOrder
     */
    public function addProduct(\Enmash\Bundle\StoreBundle\Entity\ProductOrder $products)
    {
        $this->products[] = $products;
        $products->setOrder($this);

        return $this;
    }

    /**
     * Remove products
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\ProductOrder $products
     */
    public function removeProduct(\Enmash\Bundle\StoreBundle\Entity\ProductOrder $products)
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
