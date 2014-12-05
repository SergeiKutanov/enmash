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
use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

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

            /* @var $breadcrumbs Breadcrumbs */
            $breadcrumbs = $this->get('white_october_breadcrumbs');

            $node = $category;
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

            return $this->render(
                'EnmashPagesBundle:Pages:Catalog/index.html.twig'
            );
        } else {
            $em = $this->getDoctrine()->getManager();
            $products = $em
                ->getRepository('EnmashStoreBundle:Product')
                ->findAllElementsForCategory(
                    $category
                );

            $serializer = $this->container->get('serializer');

            $response = array(
                'products' => array(),
                'category' => $category
            );
            /* @var $product Product */
            foreach ($products as $product) {
                $response['products'][] = $product;
            }

            $jsonResponse = $serializer->serialize($response, 'json');

            return new Response($jsonResponse);
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
