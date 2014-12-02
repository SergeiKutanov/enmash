<?php

namespace Enmash\Bundle\PagesBundle\Controller;

use Enmash\Bundle\StoreBundle\Entity\Category;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/catalog")
 */
class CatalogController extends Controller
{
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
     * @Method("GET")
     * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
     */
    public function showSingleCategoryAction(Category $category) {
        return $this->render(
            'EnmashPagesBundle:Pages:Catalog/base.html.twig',
            array(
//                'catalog'   => $catalog
            )
        );
    }


    /**
     * @Route("/test/test")
     * @Method("POST")
     */
    public function testCatalogAction() {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('EnmashStoreBundle:Product')
            ->findAll();
        $products = array_slice($products, 0, 5);

        $jsonProducts = array();
        /* @var $product Product */
        foreach ($products as $product) {
            $jsonProducts[] = array(
                'title' => $product->getAcronym(),
                'img'   => '#',
                'desc'  => $product->getName()
            );
        }

        return new JsonResponse($jsonProducts);

    }

    /**
     * @Route("/test/test/more")
     * @Method("POST")
     */
    public function testTestCatalogAction() {
        $em = $this->getDoctrine()->getManager();
        $products = $em
            ->getRepository('EnmashStoreBundle:Product')
            ->findAll();
        $products = array_slice($products, 5, 5);

        $jsonProducts = array();
        /* @var $product Product */
        foreach ($products as $product) {
            $jsonProducts[] = array(
                'title' => $product->getAcronym(),
                'img'   => '#',
                'desc'  => $product->getName()
            );
        }

        return new JsonResponse($jsonProducts);

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
}
