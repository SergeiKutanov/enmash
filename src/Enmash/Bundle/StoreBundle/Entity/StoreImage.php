<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/16/14
 * Time: 2:08 PM
 */

namespace Enmash\Bundle\StoreBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseMedia;

/**
 * Class StoreImage
 * @package Enmash\Bundle\StoreBundle\Entity
 * @ORM\Table("StoreImage")
 * @ORM\Entity
 */
class StoreImage extends BaseMedia{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="storeImages")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="id")
     */
    private $store;


    /**
     * Set store
     *
     * @param \Enmash\Bundle\StoreBundle\Entity\Store $store
     * @return StoreImage
     */
    public function setStore(\Enmash\Bundle\StoreBundle\Entity\Store $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get store
     *
     * @return \Enmash\Bundle\StoreBundle\Entity\Store 
     */
    public function getStore()
    {
        return $this->store;
    }

    public function getId()
    {
        return $this->id;
    }
}
