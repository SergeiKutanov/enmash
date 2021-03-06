<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 11/19/14
 * Time: 1:14 PM
 */

namespace Enmash\Bundle\PagesBundle\Menu;


use Doctrine\ORM\EntityManager;
use Enmash\Bundle\StoreBundle\Entity\Category;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class Builder extends ContainerAware {

    public function catalogMainMenu(FactoryInterface $factory, array $options) {

        $menu = $factory->createItem('root');

        /* @var $em EntityManager */
        $em = $this->container->get('doctrine')->getManager();
        $catalog = $em
            ->getRepository('EnmashStoreBundle:Category')
            ->findBy(
                array(
                    'parentCategory'    => null
                ),
                array(
                    'id'
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

    public function catalogMobileMenu(FactoryInterface $factoryInterface, array $options) {
        $request = $this->container->get('request');
        $categorySlug = $request->get('slug');

        $menu = $factoryInterface->createItem('root');

        $em = $this->container->get('doctrine');
        if (!$categorySlug) {
            $categories = $em
                ->getRepository(
                    'EnmashStoreBundle:Category'
                )
                ->getSortedCategories();
            foreach ($categories as $category) {
                /* @var $category Category */
                $options['route'] = 'catalog-category-page';
                $options['routeParameters'] = array(
                    'slug'  => $category->getSlug()
                );
                $menu->addChild(
                    $category->getName(),
                    $options
                );
                if ($category->getSlug() == $categorySlug) {
                    $menu[$category->getName()]->setCurrent(true);
                }
            }

        }

        return $menu;
    }

    public function catalogSidebarSubMenu(FactoryInterface $factory, array $options) {
        $request = $this->container->get('request');
        $categorySlug = $request->get('slug');

        $menu = null;

        $em = $this->container->get('doctrine')->getManager();
        /* @var $categories Category */
        $categories = $em
            ->getRepository('EnmashStoreBundle:Category')
            ->findOneBy(
                array(
                    'slug'  => $categorySlug
                )
            );
        if ($categories) {

            $menu = $factory->createItem($categories->getName());

            if (count($categories->getSubCategories()) == 0) {
                $categories = $categories->getParentCategory();
            }

            $subCategories = $em
                ->getRepository(
                    'EnmashStoreBundle:Category'
                )
                ->getSortedCategories($categories);

            foreach ($subCategories as $category) {
                /* @var $category Category */

                $options['route'] = 'catalog-category-page';
                $options['routeParameters'] = array(
                    'slug'  => $category->getSlug()
                );

                $menu->addChild(
                    $category->getName(),
                    $options
                );

                if ($category->getSlug() == $categorySlug) {
                    $menu[$category->getName()]->setCurrent(true);
                }
            }
        }

        if (!$menu) {
            $menu = $factory->createItem('root');
        }

        return $menu;
    }

    public function catalogSidebarMenu(FactoryInterface $factory, array $options) {

        $request = $this->container->get('request');
        $categorySlug = $request->get('slug');

        $menu = $factory->createItem('root');

        $em = $this->container->get('doctrine')->getManager();
        $catalog = $em
            ->getRepository('EnmashStoreBundle:Category')
            ->getSortedCategories();

//        $catalog = $em
//            ->getRepository('EnmashStoreBundle:Category')
//            ->findBy(
//                array(
//                    'parentCategory'    => null,
//                ),
//                array(
//                    'position'  => 'ASC'
//                )
//            );

        foreach ($catalog as $item) {
            /* @var $item \Enmash\Bundle\StoreBundle\Entity\Category */

            $options = array(
                'route' => 'catalog-category-page',
                'routeParameters' => array(
                    'slug' => $item->getSlug()
                )
            );

            if ($item->getSubCategories()) {
                $options['attributes'] = array(
                    'class'     => 'has-children'
                );
            }

            $menu->addChild(
                $item->getName(),
                $options
            );

            if ($item->getSlug() == $categorySlug) {
                $menu[$item->getName()]->setCurrent(true);
            }

            if ($item->getSubCategories()) {
                $this->addSecondLevel($item, $menu, $categorySlug);
            }
        }

        return $menu;

    }

    protected function addSecondLevel($item, $menu, $categorySlug){

        $em = $this
            ->container
            ->get('doctrine')
            ->getManager();

        $subCategories = $em
            ->getRepository(
                'EnmashStoreBundle:Category'
            )
            ->getSortedCategories($item);

        foreach ($subCategories as $subCategory) {
            $menu[$item->getName()]->addChild(
                $subCategory->getName(),
                array(
                    'route'             => 'catalog-category-page',
                    'routeParameters'   => array(
                        'slug'  => $subCategory->getSlug()
                    ),
                    'extras'    => array(
                        'slug'  => $subCategory->getSlug()
                    )
                )
            );

            if ($subCategory->getSlug() == $categorySlug) {
                $menu[$item->getName()][$subCategory->getName()]->setCurrent(true);
            }

            /* @var $subCategory Category */
            if ($subCategory->getSubCategories()) {
                $this->addThirdLevel($subCategory, $item, $menu, $categorySlug);
            }
        }
    }

    protected function addThirdLevel($subCategory, $item, $menu, $categorySlug) {

        $em = $this
            ->container
            ->get('doctrine')
            ->getManager();

        $subCategories = $em
            ->getRepository(
                'EnmashStoreBundle:Category'
            )
            ->getSortedCategories($subCategory);

        foreach ($subCategories as $subSubCategory) {
            $menu[$item->getName()][$subCategory->getName()]->addChild(
                $subSubCategory->getName(),
                array(
                    'route'             => 'catalog-category-page',
                    'routeParameters'   => array(
                        'slug'  => $subSubCategory->getSlug()
                    )
                )
            );

            if ($subSubCategory->getSlug() == $categorySlug) {
                $menu[$item->getName()][$subCategory->getName()][$subSubCategory->getName()]->setCurrent(true);
            }

        }
    }

} 