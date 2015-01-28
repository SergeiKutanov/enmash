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
use Enmash\Bundle\PagesBundle\Component\RedirectResponseWithCookie;
use Enmash\Bundle\PagesBundle\Entity\Article;
use Enmash\Bundle\PagesBundle\EventListener\LocationChangeEventListener;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Enmash\Bundle\StoreBundle\Entity\SpecialOffer;
use Enmash\Bundle\StoreBundle\Entity\Store;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\Cookie;
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

class PagesController extends BaseController{

    const COOKIE_LOCATION = 'userlocation';

    /**
     * @Route("/", name="index-page")
     * @Method("GET")
     */
    public function indexAction(Request $request) {

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
                ),
                array(
                    'position'  => 'ASC'
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
                'catalog'           => $catalog,
                'city'              => $request->attributes->get(LocationChangeEventListener::LOCATION_COOKIE_NAME)
            )
        );

    }

    /**
     * @Route("/about", name="about-page")
     * @Method("GET")
     */
    public function aboutAction() {

        $this->setSeoData(
            'О Компании',
            'Описание электротехнической компании "Энергомаш"',
            'Электротехническая компания, Энергомаш, продажа электротоваров'
        );

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

        $storeCount = count($stores);

        $this->setSeoData(
            'Магизины',
            "Электротехническая компания \"Энергомаш\" объединяет в своей сети $storeCount магазинов, 6 из которых располагаются во Владимире.",
            'Магазины Энергомаш, найти магазин Энергомаш, адреса магазинов'
        );

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

        $storeCount = count($stores);

        $this->setSeoData(
            'Службы сбыта и столы заказов',
            "Сеть электротехнической компании \"Энергомаш\" состоит из $storeCount служб сбыта и столов заказов, работающих по безналичному расчету.",
            'Службы сбыта Энергомаш, найти службу сбыта Энергомаш, столы заказов Энергомаш, найти стол заказов Энергомаш'
        );

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

        $this->setSeoData(
            'Контакты',
            'Список контактов электротехнической компании "Энергомаш"',
            'Список контактов Энергомаш, телефоны компании Энергомаш'
        );

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

        $this->setSeoData(
            'Ваша прибыль',
            'Прибыль и выгода в магазинах электротехнической компании "Энергомаш"',
            'Прибыль, выгода, Энергомаш'
        );

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

        $this->setSeoData(
            'Ваша безопасность',
            'Статьи о безопасности товаров Энергомаш',
            'Безопасность, подсказки, Энергомаш'
        );

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

        $this->setSeoData(
            'Специальные предложения',
            'Специальные предложения электротехнической компании "Энергомаш"',
            'Скидки, специальные предложения, электротехническая компания Энергомаш'
        );

        $em = $this->getDoctrine()->getManager();
        $discounts = $em
            ->getRepository('EnmashStoreBundle:SpecialOffer')
            ->findBy(
                array(
                    'type'      => SpecialOffer::TYPE_DISCOUNT,
                    'publish'   => true
                )
            );

        $offers = $em
            ->getRepository('EnmashStoreBundle:SpecialOffer')
            ->findBy(
                array(
                    'type'      => SpecialOffer::TYPE_SPECIAL_OFFER,
                    'publish'   => true
                )
            );

        $bonus = $em
            ->getRepository('EnmashStoreBundle:SpecialOffer')
            ->findOneBy(
                array(
                    'type'      => SpecialOffer::TYPE_BONUS,
                    'publish'   => true
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

        $this->setSeoData(
            $article->getTitle(),
            'Статья на тему: ' . $article->getTitle(),
            'Статья, ' . $article->getTitle()
        );

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
     * @Route("/setCity/{store}", name="setcity")
     * @ParamConverter("store", class="EnmashStoreBundle:Store")
     */
    public function setCityAction(Store $store, Request $request) {
        $expDate = new \DateTime();
        $expDate->add(new \DateInterval('P1D'));
        $cookie = new Cookie(
            self::COOKIE_LOCATION,
            $store->getCity(),
            $expDate
        );

        return new RedirectResponseWithCookie(
            $request->headers->get('referer'),
            302,
            array(
                $cookie
            )
        );

    }

} 