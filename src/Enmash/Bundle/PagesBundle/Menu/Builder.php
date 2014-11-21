<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 11/19/14
 * Time: 1:14 PM
 */

namespace Enmash\Bundle\PagesBundle\Menu;


use Enmash\Bundle\StoreBundle\Entity\Category;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Routing\Router;

class Builder extends ContainerAware {

    public function catalogMainMenu(FactoryInterface $factory, array $options) {

        $menu = $factory->createItem('root');

        $em = $this->container->get('doctrine')->getManager();
        $catalog = $em
            ->getRepository('EnmashStoreBundle:Category')
            ->findBy(
                array(
                    'parentCategory'    => null
                )
            );

        foreach ($catalog as $item) {
            $menu->addChild(
                $item->getName(),
                array(
                    'route'             => 'catalog-category-page',
                    'routeParameters'   => array(
                        'slug'  => $item->getSlug()
                    )
                )
            );
        }

        return $menu;

    }

    public function catalogSidebarMenu(FactoryInterface $factory, array $options) {

        $menu = $factory->createItem('root');

        $em = $this->container->get('doctrine')->getManager();
        $catalog = $em
            ->getRepository('EnmashStoreBundle:Category')
            ->findBy(
                array(
                    'parentCategory'    => null
                )
            );

        foreach ($catalog as $item) {
            /* @var $item \Enmash\Bundle\StoreBundle\Entity\Category */
            $menu->addChild(
                $item->getName(),
                array(
                    'route'             => 'catalog-category-page',
                    'routeParameters'   => array(
                        'slug'  => $item->getSlug()
                    )
                )
            );
            if ($item->getSubCategories()) {
                $this->addSecondLevel($item, $menu);
            }
        }

        return $menu;

    }

    protected function addSecondLevel($item, $menu){
        foreach ($item->getSubCategories() as $subCategory) {
            $menu[$item->getName()]->addChild(
                $subCategory->getName(),
                array(
                    'route'             => 'catalog-category-page',
                    'routeParameters'   => array(
                        'slug'  => $subCategory->getSlug()
                    )
                )
            );
            /* @var $subCategory Category */
            if ($subCategory->getSubCategories()) {
                $this->addThirdLevel($subCategory, $item, $menu);
            }
        }
    }

    protected function addThirdLevel($subCategory, $item, $menu) {
        foreach ($subCategory->getSubCategories() as $subSubCategory) {
            $menu[$item->getName()][$subCategory->getName()]->addChild(
                $subSubCategory->getName(),
                array(
                    'route'             => 'catalog-category-page',
                    'routeParameters'   => array(
                        'slug'  => $subSubCategory->getSlug()
                    )
                )
            );
        }
    }

} 