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

    const STATUS_PENDING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_FINISHED = 3;

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
     * @var integer
     * @ORM\Column(name="status", type="smallint", options={"defauts": 1})
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private $comments;

    public static function getChoiceList() {
        return array(
            self::STATUS_PENDING => self::getChoiceString(self::STATUS_PENDING),
            self::STATUS_IN_PROGRESS => self::getChoiceString(self::STATUS_IN_PROGRESS),
            self::STATUS_FINISHED => self::getChoiceString(self::STATUS_FINISHED)
        );
    }

    public static function getChoiceString($choice) {
        switch ($choice) {
            case self::STATUS_PENDING: return 'Не обработан';
            case self::STATUS_IN_PROGRESS: return 'В обработке';
            case self::STATUS_FINISHED: return 'Закончен';
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
        $this->setStatus(self::STATUS_PENDING);
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

    /**
     * Set status
     *
     * @param integer $status
     * @return PaymentOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return PaymentOrder
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }
}
