<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/4/14
 * Time: 2:30 PM
 */

namespace Enmash\Bundle\PagesBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @Template("EnmashPagesBundle:Pages:index.html.twig")
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/getStoresCoordinates", name="getStoresCoordinates")
     * @Method("POST")
     */
    public function getStoresAction() {
        $em = $this->getDoctrine()->getManager();

        $stores = $em
            ->getRepository('EnmashStoreBundle:Store')
            ->findAll();

        $data = array();
        foreach ($stores as $store) {
            /* @var $store \Enmash\Bundle\StoreBundle\Entity\Store */
            $data[] = array(
                'address'   => $store->getAddress(),
                'longitude' => $store->getLongitude(),
                'latitude'  => $store->getLatitude(),
                'contact'   => $store->getContact()
            );
        }

        return new JsonResponse($data);
    }

} 