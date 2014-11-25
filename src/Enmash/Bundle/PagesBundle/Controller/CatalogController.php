<?php

namespace Enmash\Bundle\PagesBundle\Controller;

use Enmash\Bundle\StoreBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
                'catalog'   => $catalog
            )
        );
    }

    /**
     * @Route("/{slug}", name="catalog-category-page")
     * @Method("GET")
     * @ParamConverter("category", options={"mapping": {"slug": "slug"}})
     */
    public function showSingleCategoryAction(Category $category) {
        die();
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
