<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 23.03.15
 * Time: 12:35
 */

namespace Enmash\Bundle\PagesBundle\Entity;


use Doctrine\ORM\EntityRepository;
use Enmash\Bundle\StoreBundle\Entity\Category;

class CatalogSidebarBannerRepository extends EntityRepository {

    public function findBannersForCategory(Category $category)
    {
        $builder = $this->createQueryBuilder('sbb');
        $builder->join(
                'sbb.categories',
                'c',
                'WITH',
                $builder->expr()->in('c.id', $category->getId())
            );
        $result = $builder->getQuery()->execute();
        return $result;
    }

}