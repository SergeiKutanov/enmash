<?php
/**
 * Created by PhpStorm.
 * User: sergei
 * Date: 10/4/14
 * Time: 2:30 PM
 */

namespace Enmash\Bundle\PagesBundle\Controller;


use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;
use Doctrine\DBAL\Connection;
use Enmash\Bundle\PagesBundle\Entity\Article;
use Enmash\Bundle\StoreBundle\Entity\Product;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

        $featuredArticles = $em
            ->getRepository('EnmashPagesBundle:Article')
            ->findBy(
                array(
                    'publish'   => true,
                    'featured'  => true
                )
            );

        $cheapBannerPath = $this->container->getParameter('cheap_banner_folder') . DIRECTORY_SEPARATOR .$this->container->getParameter('cheap_banner_filename');

        return $this->render(
            'EnmashPagesBundle:Pages:index.html.twig',
            array(
                'stores'            => $sortedStores,
                'articles'          => $featuredArticles,
                'cheapbannerpath'   => $cheapBannerPath
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
    public function wholesaleStoresAction() {

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
     * @Route("/contacts", name="contacts-page")
     * @Method("GET")
     */
    public function contactsPageAction() {
        $em = $this->getDoctrine()->getManager();
        $stores = $em
            ->getRepository('EnmashStoreBundle:Store')
            ->findBy(
                array(
                    'publish'   => true
                )
            );

        return $this->render(
            'EnmashPagesBundle:Pages:contacts.html.twig',
            array(
                'stores'    => $stores
            )
        );
    }

    /**
     * @Route("/articles", name="articles-page")
     * @Method("GET")
     */
    public function articlesPage() {
        $em = $this->getDoctrine()->getManager();
        $articles = $em
            ->getRepository('EnmashPagesBundle:Article')
            ->findBy(
                array(
                    'publish'   => true
                ),
                array(
                    'featured'    => 'desc',
                    'createdAt'   => 'asc'
                )
            );
        return $this->render(
            'EnmashPagesBundle:Pages:articles.html.twig',
            array(
                'articles'  => $articles
            )
        );
    }

    /**
     * @Route("/specialoffers", name="special-offers-page")
     * @Method("GET")
     */
    public function specialOfferPageAction() {

        $em = $this->getDoctrine()->getManager();
        $offers = $em
            ->getRepository('EnmashStoreBundle:SpecialOffer')
            ->findAll();

        return $this->render(
            'EnmashPagesBundle:Pages:specialoffers.html.twig',
            array(
                'offers'  => $offers
            )
        );
    }

    /**
     * @Route("/article/{slug}", name="article-page")
     * @Method("GET")
     * @ParamConverter("article", class="EnmashPagesBundle:Article", options={"mapping": {"slug": "slug"}})
     */
    public function articlePageAction(Article $article) {
        return $this->render(
            'EnmashPagesBundle:Pages:article.html.twig',
            array(
                'article'   => $article
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

            $contactString = 'Тел.: ';
            foreach ($store->getContacts() as $contact) {
                if ($contact->getPhone()) {
                    //todo remove trailing coma
                    $contactString .= $contact->getPhone() . ', ';
                }
            }


            /* @var $store \Enmash\Bundle\StoreBundle\Entity\Store */
            $data[] = array(
                'address'   => $store->getAddress(),
                'longitude' => $store->getLongitude(),
                'latitude'  => $store->getLatitude(),
                'contact'   => $contactString,
                'schedule'  => $store->getSchedule(),
                'link'      => "#store_" . $store->getId(),
                'uri'       => $path,
                'storeType' => $store->getStoreTypeString($store->getStoreType())
            );



        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/test")
     */
    public function testAction() {

        $article = new Article();
        $article->setTitle('Заголовок статьи');
        $article->setAbstract('abstract');
        $article->setBody('body');
        $article->setPublish(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
    }

} 