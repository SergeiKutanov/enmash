<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 27.01.15
 * Time: 10:56
 */

namespace Enmash\Bundle\StoreBundle\Entity;


use Doctrine\ORM\EntityRepository;

class EnbVmProductRepository extends EntityRepository{

    public function findProductsWithPictures() {
        return $this
            ->getEntityManager()
            ->createQuery(
                "SELECT p FROM EnmashStoreBundle:EnbVmProduct p WHERE p.productFullImage > :empty_string"
            )
            ->setParameter(
                ':empty_string',
                0
            )
            ->getResult();
    }

}