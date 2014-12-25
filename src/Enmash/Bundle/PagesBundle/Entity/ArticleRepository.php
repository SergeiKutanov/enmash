<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 12.12.14
 * Time: 11:21
 */

namespace Enmash\Bundle\PagesBundle\Entity;


use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository {

    public function getRandomArticles($count) {
        $result = $this->createQueryBuilder('a')
            ->where(
                'a.featured = true'
            )
            ->andWhere(
                'a.publish = true'
            )
            ->getQuery()
            ->getResult();

        shuffle($result);

        return array_slice($result, 0, $count);
    }

    public function getYourBenefitArticles() {

        $result = $this->createQueryBuilder('a')
            ->where('a.publish = true')
            ->andWhere('a.title != :title')
            ->andWhere('a.featured = true')
            ->getQuery()
            ->setParameters(
                array(
                    'title' => Article::NEED_TO_KNOW_ARTICLE_TITLE
                )
            )
            ->getResult();
        return $result;
    }


    public function getYourSafetyArticles() {

        $result = $this->createQueryBuilder('a')
            ->where('a.publish = true')
            ->andWhere('a.title = :title')
            ->andWhere('a.featured = true')
            ->getQuery()
            ->setParameters(
                array(
                    'title' => Article::NEED_TO_KNOW_ARTICLE_TITLE
                )
            )
            ->getResult();
        return $result;
    }
} 