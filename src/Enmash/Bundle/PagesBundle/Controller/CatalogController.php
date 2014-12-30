<?php

namespace Enmash\Bundle\PagesBundle\Controller;

use Enmash\Bundle\StoreBundle\Entity\Category;
use Enmash\Bundle\StoreBundle\Entity\Product;
use JMS\Serializer\Serializer;
use Sonata\MediaBundle\Model\GalleryHasMedia;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/catalog")
 */
class CatalogController extends Controller
{
    const PAGINATION_LIMIT = 12;

    /**
     * @Route("", name="catalog-index-page")
     * @Method("GET")
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager();
        $catalog = $em
            ->getRepository('EnmashStoreBundle:Category')
            ->findBy(
                array(
                    'parentCategory'    => null
                )
            );

        return $this->render(
            'EnmashPagesBundle:Pages:Catalog/base.html.twig',
            array(
//                'catalog'   => $catalog
            )
        );
    }

    /**
     * @Route("/{slug}", name="catalog-category-page")
     * @Method({"GET"})
     * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
     */
    public function showSingleCategoryAction(Category $category, Request $request) {
        $this->buildBreadcrumbs($category);

        $products = $category->getAllProducts();

        return $this->render(
            'EnmashPagesBundle:Pages:Catalog/singlecategory.html.twig',
            array(
                'category'  => $category,
                'products'  => $this->paginate($products)
            )
        );
    }

    protected function paginate($items) {
        $paginator = $this->get('knp_paginator');
        return $paginator->paginate(
            $items,
            null,
            self::PAGINATION_LIMIT
        );
    }

        //polymer json stuff
//    /**
//     * @Route("/{slug}", name="catalog-category-page")
//     * @Method({"GET", "POST"})
//     * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
//     */
//    public function showSingleCategoryAction(Category $category, Request $request) {
//        if ($request->getMethod() === 'GET') {
//
//            $this->buildBreadcrumbs($category);
//
//            return $this->render(
//                'EnmashPagesBundle:Pages:Catalog/index.html.twig'
//            );
//        } else {
//            $em = $this->getDoctrine()->getManager();
//            $products = $em
//                ->getRepository('EnmashStoreBundle:Product')
//                ->findAllElementsForCategory(
//                    $category
//                );
//
//            $serializer = $this->container->get('serializer');
//
//            $response = array(
//                'products' => array(),
//                'category' => $category
//            );
//            /* @var $product Product */
//            foreach ($products as $product) {
//
//                $image = Product::NO_IMAGE;
//
//                if ($product->getProductImages()) {
//                    $images = $product->getProductImages()->getGalleryHasMedias();
//                    $image = $this->container->get('sonata.media.twig.extension')->path($images[0]->getMedia(), 'big');
//                }
//
//                $response['products'][] = array(
//                    'product'   => $product,
//                    'image'     => $image,
//                    'href'      => $this->generateUrl('catalog-product-page', array(
//                            'slug'  => $product->getCategory()->getSlug(),
//                            'product'   => $product->getId()
//                        ))
//                );
//            }
//
//            $jsonResponse = $serializer->serialize($response, 'json');
//
//            return new Response($jsonResponse);
//        }
//
//    }

    /**
     * @Route("/{slug}/{product}", name="catalog-product-page")
     * @Method({"GET", "POST"})
     * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("product")
     */
    public function showSingleProductAction(Category $category, Product $product, Request $request) {

        if ($request->getMethod() == 'GET') {

            $this->buildBreadcrumbs($category);

            return $this->render(
                'EnmashPagesBundle:Pages:Catalog/singleitem.html.twig',
                array(
                    'product'   => $product
                )
            );
        }

        $serializer = $this->get('serializer');
        /* @var $serializer Serializer */
        $productJson = $serializer->serialize($product, 'json');
        return new Response($productJson);
    }

    /**
     * @Route("/polymer/elements/{name}", name="get_polymer_element")
     * @Method("GET")
     */
    public function getPolymerElement($name) {
        return $this->render(
            'EnmashPagesBundle:Polymer:Elements/' . $name
        );
    }

    private function buildBreadcrumbs($node) {
        /* @var $breadcrumbs Breadcrumbs */
        $breadcrumbs = $this->get('white_october_breadcrumbs');

        while($node) {
            $breadcrumbs->prependItem(
                $node->getName(),
                $this->get('router')->generate(
                    'catalog-category-page',
                    array(
                        'slug'  => $node->getSlug()
                    )
                )
            );
            $node = $node->getParentCategory();
        }
        $breadcrumbs->prependItem(
            'Каталог',
            $this->get('router')->generate('catalog-index-page')
        );
    }

}
