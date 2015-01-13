<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 13.01.15
 * Time: 11:33
 */

namespace Enmash\Bundle\PagesBundle\Component;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;

class EnmashServices {

    private $doctrine;

    public function __construct(Registry $registry) {
        $this->doctrine = $registry;
    }

    public function getSortedStores() {
        $sortedStores = $this
            ->doctrine
            ->getRepository("EnmashStoreBundle:Store")
            ->getCitySortedStores();
        return $sortedStores;
    }

}