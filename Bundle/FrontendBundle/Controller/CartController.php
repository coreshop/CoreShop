<?php

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;

class CartController extends FrontendController
{
    public function widgetAction(Request $request)
    {
        if ( $this->get('templating')->exists('CoreShopFrontendBundle:Cart:_widget.html.twig') ) {

        }

        //@AppBundle:Sola

        return $this->render('CoreShopFrontendBundle:Cart:_widget.html.twig', [
            'cart' => $this->getCart()
        ]);
    }

    public function addItemAction(Request $request, $productId) {
        $product = $this->get('coreshop.repository.product')->find($productId);
        $quantity = 1;

        $this->addCartItem($product, $quantity);

        //TODO: Flashes

        return $this->redirectToRoute('coreshop_shop_cart_summary');
    }

    public function removeItemAction(Request $request, $cartItemId) {
        $cartItem = $this->get('coreshop.repository.cart_item')->find($cartItemId);

        if (!$cartItem instanceof CartItemInterface) {
            return $this->redirectToRoute('coreshop_shop_index');
        }

        if ($cartItem->getCart()->getId() !== $this->getCart()->getId()) {
            return $this->redirectToRoute('coreshop_shop_index');
        }

        //TODO: add flash

        $this->removeCartItem($cartItem);

        return $this->redirectToRoute('coreshop_shop_cart_summary');
    }

    public function summaryAction(Request $request) {
        return $this->render('CoreShopFrontendBundle:Cart:summary.html.twig', [
            'cart' => $this->getCart(),
            'editAllowed' => true,
            'checkoutSteps' => $this->get('coreshop.checkout_manager')->getSteps(),
            'currentCheckoutStep' => 0
        ]);
    }

    /**
     * @param ProductInterface $product
     * @param int $quantity
     * @param bool $increaseAmount
     * @return bool|CartItemInterface|null
     *
     * @todo: move to service?
     */
    private function updateCartItemQuantity(ProductInterface $product, $quantity = 0, $increaseAmount = false) {
        $cart = $this->getCart();

        $item = $cart->getItemForProduct($product);

        if ($item instanceof CartItemInterface) {
            if ($quantity <= 0) {
                $this->removeCartItem($item);

                return false;
            }

            $newQuantity = $quantity;

            if ($increaseAmount) {
                $currentQuantity = $item->getQuantity();

                if (is_int($currentQuantity)) {
                    $newQuantity = $currentQuantity + $quantity;
                }
            }

            $item->setQuantity($newQuantity);
            $item->save();
        }
        else {
            /**
             * @var $item CartItemInterface
             */
            $item = $this->get('coreshop.factory.cart_item')->createNew();
            $item->setKey(uniqid());
            $item->setParent($cart);
            $item->setQuantity($quantity);
            $item->setProduct($product);
            $item->setPublished(true);
            $item->save();

            $cart->addItem($item);
            $cart->save();
        }
        
        return $item;
    }

    /**
     * @param ProductInterface $product
     * @param int $quantity
     * @return CartItemInterface|null
     */
    private function addCartItem(ProductInterface $product, $quantity = 1) {
        $this->getCartManager()->persistCart($this->getCart());

        return $this->updateCartItemQuantity($product, $quantity, true);
    }

    /**
     * @param CartItemInterface $cartItem
     */
    private function removeCartItem(CartItemInterface $cartItem) {
        $cartItem->delete();
    }

    /**
     * @return \CoreShop\Component\Order\Model\CartInterface
     */
    private function getCart() {
        return $this->getCartManager()->getCart();
    }

    /**
     * @return \CoreShop\Bundle\OrderBundle\Manager\CartManager
     */
    private function getCartManager() {
        return $this->get('coreshop.cart.manager');
    }
}
