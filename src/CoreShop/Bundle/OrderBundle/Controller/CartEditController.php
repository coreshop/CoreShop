<?php

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Bundle\OrderBundle\Form\Type\AddMultipleToCartType;
use CoreShop\Bundle\OrderBundle\Form\Type\EditCartType;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CartEditController extends AbstractSaleController
{
    /**
     * @param Request $request
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

        $this->get('coreshop.cart.manager')->persistCart($cart);

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @param Request $request
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

            if (!$product instanceof PurchasableInterface)
            {
                return new JsonResponse([
                    'success' => false,
                ]);
            }

            $cartItem = $this->get('coreshop.factory.cart_item')->createWithPurchasable($product, $quantity);

            $commands[] = $this->createAddToCart($cart, $cartItem);
        }

        $form = $this->get('form.factory')->createNamed('', AddMultipleToCartType::class, ['items' => $commands]);

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $addsToCart = $form->getData();

                /**
                 * @var AddToCartInterface $addToCart
                 */
                foreach ($addsToCart['items'] as $addToCart) {
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
     * @param CartInterface $cart
     * @param CartItemInterface $item

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
