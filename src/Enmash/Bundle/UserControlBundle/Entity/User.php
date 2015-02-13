<?php

namespace Enmash\Bundle\UserControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Enmash\Bundle\StoreBundle\Entity\ShoppingCart", mappedBy="user", cascade={"all"}, orphanRemoval=true )
     */
    private $shoppingCartItems;

    /**
     * @ORM\OneToMany(targetEntity="Enmash\Bundle\StoreBundle\Entity\PaymentOrder", mappedBy="user", cascade={"all"}, orphanRemoval=true )
     */
    private $paymentOrders;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct() {
        parent::__construct();

        $this->roles = array('ROLE_SITE_USER');
    }

    /**
     * Add shoppingCartItems
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\ShoppingCart $shoppingCartItems
     * @return User
     */
    public function addShoppingCartItem(\Enmash\Bundle\StoreBundle\Entity\ShoppingCart $shoppingCartItems)
    {
        $this->shoppingCartItems[] = $shoppingCartItems;

        return $this;
    }

    /**
     * Remove shoppingCartItems
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\ShoppingCart $shoppingCartItems
     */
    public function removeShoppingCartItem(\Enmash\Bundle\StoreBundle\Entity\ShoppingCart $shoppingCartItems)
    {
        $this->shoppingCartItems->removeElement($shoppingCartItems);
    }

    /**
     * Get shoppingCartItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShoppingCartItems()
    {
        return $this->shoppingCartItems;
    }

    /**
     * Add paymentOrders
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\PaymentOrder $paymentOrders
     * @return User
     */
    public function addPaymentOrder(\Enmash\Bundle\StoreBundle\Entity\PaymentOrder $paymentOrders)
    {
        $this->paymentOrders[] = $paymentOrders;

        return $this;
    }

    /**
     * Remove paymentOrders
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\PaymentOrder $paymentOrders
     */
    public function removePaymentOrder(\Enmash\Bundle\StoreBundle\Entity\PaymentOrder $paymentOrders)
    {
        $this->paymentOrders->removeElement($paymentOrders);
    }

    /**
     * Get paymentOrders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaymentOrders()
    {
        return $this->paymentOrders;
    }

}
