<?php

namespace Enmash\Bundle\StoreBundle\Controller;

use Enmash\Bundle\PagesBundle\Component\RedirectResponseWithCookie;
use Enmash\Bundle\StoreBundle\Entity\PaymentOrder;
use Enmash\Bundle\StoreBundle\Entity\Product;
use Enmash\Bundle\StoreBundle\Entity\ProductOrder;
use Enmash\Bundle\StoreBundle\Entity\ShoppingCart;
use Enmash\Bundle\UserControlBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ShoppingCartController extends Controller
{

    const COOKIE_NAME = 'shopping-cart';

    /**
     * @Route("/shopping-cart/add/{product}", requirements={"product": "\d+"}, name="add-product-to-shopping-cart")
     * @ParamConverter("product", class="Enmash\Bundle\StoreBundle\Entity\Product")
     */
    public function addProductToShoppingCartAction(Product $product, Request $request) {

        $user = $this->getUser();

        if ($user) {
            $this->putItemInShoppingCart($user, $product);
        } else {
            /* @var $cookie Cookie */
            $cookie = $request->cookies->get(self::COOKIE_NAME);

            $shoppingCartItems = $this->getShoppingCartItemsFromCookie($cookie);

            $newShoppingCartItem = new ShoppingCart();
            $newShoppingCartItem->setProduct($product);
            $newShoppingCartItem->setQuantity(1);

            $shoppingCartItems = $this->addProductToShoppingCartBatch($shoppingCartItems, $newShoppingCartItem);

            $cookie = $this->putShoppingCartItemsIntoCookie($shoppingCartItems);

            return new RedirectResponseWithCookie(
                $this->generateUrl('shopping-cart-index'),
                302,
                array(
                    $cookie
                )
            );

        }

        return $this->redirect(
            $this->generateUrl('shopping-cart-index')
        );

    }

    /**
     * @Route("/shopping-cart/remove/{product}", requirements={"product": "\d+"}, name="shopping-cart-remove-item")
     * @ParamConverter("product", class="Enmash\Bundle\StoreBundle\Entity\Product")
     */
    public function removeItemFromShoppingCartAction(Product $product, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        //logged in user
        if ($user) {
            $shoppingCartItem = $em
                ->getRepository('EnmashStoreBundle:ShoppingCart')
                ->findOneBy(
                    array(
                        'user'      => $user->getId(),
                        'product'   => $product->getId()
                    )
                );
            if ($shoppingCartItem) {
                $em->remove($shoppingCartItem);
                $em->flush();
            }

            return $this->redirect(
                $this->generateUrl('shopping-cart-index')
            );
        } else {
            $cookie = $request->cookies->get(self::COOKIE_NAME);
            $shoppingCartItems = $this->getShoppingCartItemsFromCookie($cookie);

            $newShoppingCartItems = array();
            foreach ($shoppingCartItems as $shoppingCartItem) {
                if ($shoppingCartItem->getProduct()->getId() != $product->getId()) {
                    $newShoppingCartItems[] = $shoppingCartItem;
                }
            }

            $cookie = $this->putShoppingCartItemsIntoCookie($newShoppingCartItems);

            return new RedirectResponseWithCookie(
                $this->generateUrl('shopping-cart-index'),
                302,
                array($cookie)
            );
        }
    }

    /**
     * @Route("/shopping-cart/view", name="shopping-cart-index")
     */
    public function indexAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $shoppingCartItems = array();

        /* @var $user User */
        $user = $this->getUser();
        if ($user) {
            $shoppingCartItems = $user->getShoppingCartItems();
        }

        if(!$shoppingCartItems) {
            $shoppingCartItems = $this->getShoppingCartItemsFromCookie(
                $request->cookies->get(self::COOKIE_NAME)
            );
        }

        return $this->render(
            'EnmashPagesBundle:ShoppingCart:index.html.twig',
            array(
                'shoppingCartItems' => $shoppingCartItems
            )
        );
    }

    /**
     * @Route("/shopping-cart/clear", name="shopping-cart-clear")
     */
    public function clearShoppingCartAction(Request $request) {

        /* @var $user User */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        if ($user) {
            foreach($user->getShoppingCartItems() as $shoppingCartItem) {
                $user->removeShoppingCartItem($shoppingCartItem);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('shopping-cart-index')
            );
        } else {
            $cookie = $this->putShoppingCartItemsIntoCookie(array());

            return new RedirectResponseWithCookie(
                $this->generateUrl('shopping-cart-index'),
                302,
                array($cookie)
            );
        }

    }

    /**
     * @Route("/shopping-cart/confirm", name="confirm-order")
     */
    public function confirmOrderAction(Request $request) {
        /* @var $user User */
        $user = $this->getUser();

        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $paymentOrder = new PaymentOrder();
            $paymentOrder->setConfirmed(true);
            $paymentOrder->setUser($user);
            $em->persist($paymentOrder);
            foreach ($user->getShoppingCartItems() as $shoppingCartItem) {
                /* @var $shoppingCartItem ShoppingCart */
                $productOrder = new ProductOrder();
                $productOrder->setProduct($shoppingCartItem->getProduct());
                $productOrder->setQuantity($shoppingCartItem->getQuantity());
                $productOrder->setPrice($shoppingCartItem->getPrice());
                $paymentOrder->addProduct($productOrder);
            }
            $em->persist($paymentOrder);

            foreach ($user->getShoppingCartItems() as $item) {
                $user->removeShoppingCartItem($item);
            }
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'Ваш заказ сформулирован. Наши менеджеры свяжутся с вами в ближайшее время'
            );


            return $this->redirect(
                $this->generateUrl('shopping-cart-index')
            );

        } else {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Заказ могут оформить только зарегестрированные пользователи'
            );

            return $this->redirect(
                $this->generateUrl('shopping-cart-index')
            );
        }
    }

    /**
     * @Route("/profile/orders/show", name="profile-show-orders")
     * @Security("has_role('ROLE_USER')")
     */
    public function showOrdersAction() {
        return $this->render(
            'EnmashPagesBundle:Profile:show_orders.html.twig'
        );
    }

    /**
     * @Route("/profile/order/remove/{paymentOrder}", name="profile-order-remove", requirements={"paymentOrder": "\d+"})
     * @ParamConverter("paymentOrder", class="Enmash\Bundle\StoreBundle\Entity\PaymentOrder")
     */
    public function removeOrder(PaymentOrder $paymentOrder) {

        if ($paymentOrder->getUser()->getId() != $this->getUser()->getId()) {
            throw new \Exception('Only owners can modify their orders!');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($paymentOrder);
        $em->flush();

        return $this->redirect(
            $this->generateUrl('profile-show-orders')
        );
    }

    private function putItemInShoppingCart(User $user, Product $product) {
        $em = $this
            ->getDoctrine()
            ->getManager();

        /* @var $shoppingCart ShoppingCart */
        $shoppingCart = $em
            ->getRepository('EnmashStoreBundle:ShoppingCart')
            ->findOneBy(
                array(
                    'user'      => $user->getId(),
                    'product'   => $product->getId()
                )
            );

        if (!$shoppingCart) {
            $shoppingCart = new ShoppingCart();
            $shoppingCart->setUser($user);
            $shoppingCart->setProduct($product);
            $shoppingCart->setQuantity(1);
        } else {
            $shoppingCart->setQuantity(
                $shoppingCart->getQuantity() + 1
            );
        }

        $em->persist($shoppingCart);
        $em->flush();

        return $shoppingCart;

    }

    private function getShoppingCartItemsFromCookie($cookie = null) {

        if (!$cookie) return array();

        $em = $this->getDoctrine()->getManager();
        $shoppingCartItems = array();
        $cookieItems = unserialize($cookie);
        foreach ($cookieItems as $cookieItem) {
            $product = $em
                ->getRepository('EnmashStoreBundle:Product')
                ->find($cookieItem->id);

            if (!$product) {
                throw new NotFoundHttpException('Product not found');
            }

            $shoppingCartItem = new ShoppingCart();
            $shoppingCartItem->setProduct($product);
            $shoppingCartItem->setQuantity($cookieItem->quantity);
            $shoppingCartItems[] = $shoppingCartItem;
        }
        return $shoppingCartItems;
    }

    private function addProductToShoppingCartBatch($shoppingCartItems, ShoppingCart $shoppingCart) {
        foreach ($shoppingCartItems as $shoppingCartItem) {
            if ($shoppingCart->getProduct()->getId() == $shoppingCartItem->getProduct()->getId()) {
                $shoppingCartItem->setQuantity(
                    $shoppingCartItem->getQuantity() + $shoppingCart->getQuantity()
                );
                $shoppingCart = null;
                break;
            }
        }

        if ($shoppingCart) {
            array_push($shoppingCartItems, $shoppingCart);
        }

        return $shoppingCartItems;

    }

    private function putShoppingCartItemsIntoCookie($shoppingCartItems) {

        $productsForCart = array();

        foreach ($shoppingCartItems as $shoppingCartItem) {
            $productForCart = new \stdClass();
            $productForCart->id = $shoppingCartItem->getProduct()->getId();
            $productForCart->quantity = $shoppingCartItem->getQuantity();
            $productsForCart[] = $productForCart;
        }

        return new Cookie(
            self::COOKIE_NAME,
            serialize($productsForCart)
        );
    }
}
