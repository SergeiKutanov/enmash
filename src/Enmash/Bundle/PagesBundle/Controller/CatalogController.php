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
use Symfony\Component\HttpFoundation\Request;

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
     * @Method({"GET", "POST"})
     * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
     */
    public function showSingleCategoryAction(Category $category, Request $request) {
        if ($request->getMethod() === 'GET') {
            return $this->render(
                'EnmashPagesBundle:Pages:Catalog/base.html.twig'
            );
        } else {
            $em = $this->getDoctrine()->getManager();
            $products = $em
                ->getRepository('EnmashStoreBundle:Product')
                ->findAllElementsForCategory(
                    $category
                );

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
