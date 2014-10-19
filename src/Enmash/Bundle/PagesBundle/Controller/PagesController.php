<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/4/14
 * Time: 2:30 PM
 */

namespace Enmash\Bundle\PagesBundle\Controller;


use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;
use Enmash\Bundle\StoreBundle\Entity\Store;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class PagesController extends Controller{

    /**
     * @Route("/", name="index-page")
     * @Method("GET")
     */
    public function indexAction() {

        //todo think of a better way to find store's location for landing page tab
        $em = $this->getDoctrine()->getManager();
        $stores = $em
            ->getRepository('EnmashStoreBundle:Store')
            ->findBy(
                array(
                    'publish'   => true
                )
            );
        $sortedStores = array(
            'vladimir'  => array()
        );
        foreach ($stores as $store) {
            /* @var $store Store */
            if (strpos($store->getAddress(), 'Владимир') !== false) {
                $sortedStores['vladimir'][] = $store;
            }
        }

        return $this->render(
            'EnmashPagesBundle:Pages:index.html.twig',
            array(
                'stores'    => $sortedStores
            )
        );

    }

    /**
     * @Route("/about", name="about-page")
     * @Method("GET")
     */
    public function aboutAction() {

        return $this->render(
            'EnmashPagesBundle:Pages:about.html.twig'
        );

    }

    /**
     * @Route("/stores", name="stores-page")
     * @Method("GET")
     */
    public function storesAction() {

        $em = $this->getDoctrine()->getManager();
        $stores = $em
            ->getRepository('EnmashStoreBundle:Store')
            ->getOnlyOneTypeOfStores(Store::RETAIL_TYPE);

        if (!$stores) {
            throw new NotFoundHttpException('No stores found');
        }

        return $this->render(
            'EnmashPagesBundle:Pages:stores.html.twig',
            array(
                'stores'    => $stores
            )
        );
    }

    /**
     * @Route("/wholesale-stores", name="wholesale-stores-page")
     * @Method("GET")
     */
    public function whilesaleStoresAction() {

        $em = $this->getDoctrine()->getManager();
        $stores = $em
            ->getRepository('EnmashStoreBundle:Store')
            ->getOnlyOneTypeOfStores(Store::WHOLESALE_TYPE);

        if (!$stores) {
            throw new NotFoundHttpException('No stores found');
        }

        return $this->render(
            'EnmashPagesBundle:Pages:wholesalestores.html.twig',
            array(
                'stores'    => $stores
            )
        );
    }

    /**
     * @Route("/getStoresCoordinates", name="getStoresCoordinates")
     * @Method("POST")
     */
    public function getStoresAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $type = $request->get('type', null);

        $stores = $em
            ->getRepository('EnmashStoreBundle:Store')
            ->getOnlyOneTypeOfStores($type);

        $data = array();
        foreach ($stores as $store) {
            $path = "#store_" . $store->getId();
            if ($store->getStoreType() == Store::RETAIL_TYPE || $store->getStoreType() == Store::BOTH_TYPE) {
                $path = $this
                        ->generateUrl('stores-page') . $path;
            } elseif ($store->getStoreType() == Store::WHOLESALE_TYPE) {
                $path = $this
                        ->generateUrl('wholesale-stores-page') . $path;
            }

            /* @var $store \Enmash\Bundle\StoreBundle\Entity\Store */
            $data[] = array(
                'address'   => $store->getAddress(),
                'longitude' => $store->getLongitude(),
                'latitude'  => $store->getLatitude(),
                'contact'   => $store->getContact(),
                'schedule'  => $store->getSchedule(),
                'link'      => "#store_" . $store->getId(),
                'uri'       => $path
            );



        }

        return new JsonResponse($data);
    }



} 