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
use Enmash\Bundle\StoreBundle\Entity\SpecialOffer;
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
        $sortedStores = $em
            ->getRepository("EnmashStoreBundle:Store")
            ->getCitySortedStores();

        $featuredArticles = $em
            ->getRepository('EnmashPagesBundle:Article')
            ->getRandomArticles(3);

        $cheapBannerPath = $this->container->getParameter('cheap_banner_folder') . DIRECTORY_SEPARATOR .$this->container->getParameter('cheap_banner_filename');

        $banners = $em
            ->getRepository('EnmashPagesBundle:Banner')
            ->findBy(
                array(
                    'isPublished'   => true
                )
            );

        $catalog = $em
            ->getRepository('EnmashStoreBundle:Category')
            ->findBy(
                array(
                    'parentCategory'    => null
                )
            );

        return $this->render(
            'EnmashPagesBundle:Pages:index.html.twig',
            array(
                'stores'            => $sortedStores,
                'articles'          => $featuredArticles,
                'cheapbannerpath'   => $cheapBannerPath,
                'banners'           => $banners,
                'catalog'           => $catalog
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
            ->getOnlyOneTypeOfStores(
                array(
                    Store::WHOLESALE_TYPE,
                    Store::ORDER_TYPE
                )
            );

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
     * @Route("/yourbenefits", name="your-benefits-page")
     * @Method("GET")
     */
    public function yourBenefitsPage() {
        $em = $this->getDoctrine()->getManager();
        $articles = $em
            ->getRepository('EnmashPagesBundle:Article')
            ->getYourBenefitArticles();
        return $this->render(
            'EnmashPagesBundle:Pages:articles.html.twig',
            array(
                'articles'  => $articles
            )
        );
    }

    /**
     * @Route("/yoursafety", name="your-safety-page")
     * @Method("GET")
     */
    public function yourSafetyPage() {
        $em = $this->getDoctrine()->getManager();
        $articles = $em
            ->getRepository('EnmashPagesBundle:Article')
            ->getYourSafetyArticles();
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
        $discounts = $em
            ->getRepository('EnmashStoreBundle:SpecialOffer')
            ->findBy(
                array(
                    'type'  => SpecialOffer::TYPE_DISCOUNT,
                )
            );

        $offers = $em
            ->getRepository('EnmashStoreBundle:SpecialOffer')
            ->findBy(
                array(
                    'type'  => SpecialOffer::TYPE_SPECIAL_OFFER
                )
            );

        $bonus = $em
            ->getRepository('EnmashStoreBundle:SpecialOffer')
            ->findOneBy(
                array(
                    'type'  => SpecialOffer::TYPE_BONUS
                )
            );

        return $this->render(
            'EnmashPagesBundle:Pages:specialoffers.html.twig',
            array(
                'discounts'  => $discounts,
                'offers'     => $offers,
                'bonus'      => $bonus
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
            if (in_array(Store::RETAIL_TYPE, $store->getStoreType())) {
                $path = $this
                        ->generateUrl('stores-page') . $path;
            } else {
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
                'storeType' => $store->getStoreTypeString($store->getStoreType()),
                'id'        => $store->getId()
            );



        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/test")
     */
    public function testAction() {

//        $arr1 = [0,1,2,3];
//        $arr2 = [4];
//        var_dump(array_intersect($arr1, $arr2));
//        die();
    }

} 