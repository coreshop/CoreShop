<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\DTO\AddMultipleToCartInterface;
use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Bundle\OrderBundle\Factory\AddMultipleToCartFactoryInterface;
use CoreShop\Bundle\OrderBundle\Factory\AddToCartFactoryInterface;
use CoreShop\Bundle\OrderBundle\Form\Type\AddMultipleToCartType;
use CoreShop\Bundle\OrderBundle\Form\Type\EditCartType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Component\Order\Cart\CartModifier;
use CoreShop\Component\Order\Factory\OrderItemFactoryInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Repository\OrderItemRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class OrderEditController extends PimcoreController
{
    public function editItemsAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        FormFactoryInterface $formFactory,
        ErrorSerializer $errorSerializer,
        CartManagerInterface $cartManager
    ): JsonResponse {
        $this->isGrantedOr403();

        $cartId = $request->get('id');
        $cart = $orderRepository->find($cartId);

        if (!$cart instanceof OrderInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Order with ID '$cartId' not found"]
            );
        }

        $form = $formFactory->createNamed('', EditCartType::class, $cart);

        if (!$request->isMethod('post')) {
            throw new MethodNotAllowedHttpException(['post']);
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->viewHandler->handle([
                'success' => false,
                'message' => $errorSerializer->serializeErrorFromHandledForm($form),
            ]);
        }

        $cart = $form->getData();

        $cartManager->persistCart($cart);

        return $this->viewHandler->handle(['success' => true]);
    }

    public function addItemsAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $purchasableStackRepository,
        OrderItemFactoryInterface $orderItemFactory,
        AddToCartFactoryInterface $addToCartFactory,
        AddMultipleToCartFactoryInterface $addMultipleToCartFactory,
        FormFactoryInterface $formFactory,
        ErrorSerializer $errorSerializer,
        CartManagerInterface $cartManager,
        CartModifier $cartModifier
    ): JsonResponse {
        $this->isGrantedOr403();

        $cartId = $request->get('id');
        $cart = $orderRepository->find($cartId);

        if (!$cart instanceof OrderInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Order with ID '$cartId' not found"]
            );
        }

        $commands = [];

        foreach ($request->get('items', []) as $product) {
            $productId = $product['cartItem']['purchasable'];
            $quantity = $product['cartItem']['quantity'] ?? 1;

            $product = $purchasableStackRepository->find($productId);

            if (!$product instanceof PurchasableInterface) {
                return new JsonResponse([
                    'success' => false,
                ]);
            }

            $cartItem = $orderItemFactory->createWithPurchasable($product, $quantity);

            $commands[] = $this->createAddToCart($addToCartFactory, $cart, $cartItem);
        }

        $addMultipleAddToCarts = $this->createMultipleAddToCart($addMultipleToCartFactory, $commands);

        $form = $formFactory->createNamed('', AddMultipleToCartType::class, $addMultipleAddToCarts);

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                /**
                 * @var AddMultipleToCartInterface $addsToCart
                 */
                $addsToCart = $form->getData();

                foreach ($addsToCart->getItems() as $addToCart) {
                    $cartModifier->addToList(
                        $addToCart->getCart(),
                        $addToCart->getCartItem()
                    );
                }

                $cartManager->persistCart($cart);

                return $this->viewHandler->handle(['success' => true]);
            }

            return new JsonResponse([
                'success' => false,
                'message' => $errorSerializer->serializeErrorFromHandledForm($form),
            ]);
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    public function removeItemAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        CartManagerInterface $cartManager,
        CartModifier $cartModifier
    ): JsonResponse {
        $this->isGrantedOr403();

        $cartId = $request->get('id');
        $cart = $orderRepository->find($cartId);

        if (!$cart instanceof OrderInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Order with ID '$cartId' not found"]
            );
        }

        $cartItemId = $request->get('cartItem');
        $cartItem = $orderItemRepository->find($cartItemId);

        if (!$cartItem instanceof OrderItemInterface) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => "Order Item with ID '$cartItemId' not found"]
            );
        }

        if ($cartItem->getOrder()->getId() !== $cart->getId()) {
            return $this->viewHandler->handle(
                ['success' => false, 'message' => 'Not allowed']
            );
        }

        $cartModifier->removeFromList($cart, $cartItem);
        $cartManager->persistCart($cart);

        return $this->viewHandler->handle(['success' => true]);
    }

    protected function createMultipleAddToCart(
        AddMultipleToCartFactoryInterface $addMultipleToCartFactory,
        array $addToCarts
    ): AddMultipleToCartInterface {
        return $addMultipleToCartFactory->createWithMultipleAddToCarts($addToCarts);
    }

    protected function createAddToCart(
        AddToCartFactoryInterface $addToCartFactory,
        OrderInterface $cart,
        OrderItemInterface $item
    ): AddToCartInterface {
        return $addToCartFactory->createWithCartAndCartItem($cart, $item);
    }
}
