<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\DTO\AddMultipleToCartInterface;
use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Bundle\OrderBundle\Form\Type\AddMultipleToCartType;
use CoreShop\Bundle\OrderBundle\Form\Type\EditCartType;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CartEditController extends AbstractSaleController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editItemsAction(Request $request)
    {
        $this->isGrantedOr403();

        $cartId = $request->get('id');
        $cart = $this->get('coreshop.repository.cart')->find($cartId);

        if (!$cart instanceof CartInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Cart with ID '$cartId' not found"]
            );
        }

//        if ($cart->getState() === PurchaseOrderStates::STATE_COMPLETE) {
//            return $this->viewHandler->handle(
//                ['success' => false, 'message' => "Purchase Order is not changeable anymore."]
//            );
//        }

        $form = $this->get('form.factory')->createNamed('', EditCartType::class, $cart);

        if (!$request->isMethod('post')) {
            throw new MethodNotAllowedHttpException(['post']);
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->viewHandler->handle(['success' => false, 'message' => $this->get('coreshop.resource.helper.form_error_serializer')->serializeErrorFromHandledForm($form)]);
        }

        $cart = $form->getData();

        InheritanceHelper::useInheritedValues(function() use ($cart) {
            $this->get('coreshop.cart_processor')->process($cart);
        }, true);

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addItemsAction(Request $request)
    {
        $this->isGrantedOr403();

        $cartId = $request->get('id');
        $cart = $this->get('coreshop.repository.cart')->find($cartId);

        if (!$cart instanceof CartInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Cart with ID '$cartId' not found"]
            );
        }

        $commands = [];

        foreach ($request->get('items', []) as $product) {
            $productId = $product['cartItem']['purchasable'];
            $quantity = $product['cartItem']['quantity'] ?? 1;

            $product = $this->get('coreshop.repository.stack.purchasable')->find($productId);

            if (!$product instanceof PurchasableInterface) {
                return new JsonResponse([
                    'success' => false,
                ]);
            }

            $cartItem = $this->get('coreshop.factory.cart_item')->createWithPurchasable($product, $quantity);

            $commands[] = $this->createAddToCart($cart, $cartItem);
        }

        $addMultipleAddToCarts = $this->createMultipleAddToCart($commands);

        $form = $this->get('form.factory')->createNamed('', AddMultipleToCartType::class, $addMultipleAddToCarts);

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                /**
                 * @var AddMultipleToCartInterface $addsToCart
                 */
                $addsToCart = $form->getData();

                foreach ($addsToCart->getItems() as $addToCart) {
                    $this->getCartModifier()->addToList(
                        $addToCart->getCart(),
                        $addToCart->getCartItem()
                    );
                }

                $this->get('coreshop.cart.manager')->persistCart($cart);

                return $this->viewHandler->handle(['success' => true]);
            }

            return new JsonResponse([
                'success' => false,
                'message' => implode('<br/>', array_map(function (FormError $error) {
                    return $error->getMessage();
                }, iterator_to_array($form->getErrors(true)))),
            ]);
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function removeItemAction(Request $request)
    {
        $this->isGrantedOr403();

        $cartId = $request->get('id');
        $cart = $this->get('coreshop.repository.cart')->find($cartId);

        if (!$cart instanceof CartInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Cart with ID '$cartId' not found"]
            );
        }

//        if ($cart->getState() === PurchaseOrderStates::STATE_COMPLETE) {
//            return $this->viewHandler->handle(
//                ['success' => false, 'message' => "Purchase Order is not changeable anymore."]
//            );
//        }

        $cartItemId = $request->get('cartItem');
        $cartItem = $this->get('coreshop.repository.cart_item')->find($cartItemId);

        if (!$cartItem instanceof CartItemInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Cart Item with ID '$cartItemId' not found"]
            );
        }

        if ($cartItem->getCart()->getId() !== $cart->getId()) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => 'Not allowed']
            );
        }

        $this->getCartModifier()->removeFromList($cart, $cartItem);
        $this->get('coreshop.cart.manager')->persistCart($cart);

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @param array $addToCarts
     * @return AddMultipleToCartInterface
     */
    protected function createMultipleAddToCart(array $addToCarts)
    {
        return $this->get('coreshop.factory.add_multiple_to_cart')->createWithMultipleAddToCarts($addToCarts);
    }

    /**
     * @param CartInterface     $cart
     * @param CartItemInterface $item
     *
     * @return AddToCartInterface
     */
    protected function createAddToCart(CartInterface $cart, CartItemInterface $item)
    {
        return $this->get('coreshop.factory.add_to_cart')->createWithCartAndCartItem($cart, $item);
    }

    /**
     * @return StorageListModifierInterface
     */
    protected function getCartModifier()
    {
        return $this->get('coreshop.cart.modifier');
    }
}
