<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Bundle\OrderBundle\Factory\AddToCartFactoryInterface;
use CoreShop\Bundle\OrderBundle\Form\Type\AddToCartType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartType;
use CoreShop\Bundle\OrderBundle\Form\Type\ShippingCalculatorType;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Cart\CartModifierInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderItemRepositoryInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class CartController extends FrontendController
{
    public function widgetAction(
        CartContextInterface $cartContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        return $this->renderTemplate($templateConfigurator->findTemplate('Cart/_widget.html'), [
            'cart' => $cartContext->getCart(),
        ]);
    }

    public function summaryAction(
        Request $request,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherCodeRepository,
        CartPriceRuleProcessorInterface $cartPriceRuleProcessor,
        CartManagerInterface $cartManager,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        OrderRepositoryInterface $orderRepository,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $cart = $cartContext->getCart();
        $form = $formFactory->create(CartType::class, $cart);
        $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->isValid()) {
            $cart = $form->getData();
            $code = $form->get('cartRuleCoupon')->getData();

            if ($code) {
                $voucherCode = $cartPriceRuleVoucherCodeRepository->findByCode($code);

                if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
                    $this->addFlash('error', $translator->trans('coreshop.ui.error.voucher.not_found'));

                    return $this->renderTemplate($templateConfigurator->findTemplate('Cart/summary.html'), [
                        'cart' => $cart,
                        'form' => $form->createView(),
                    ]);
                }

                $priceRule = $voucherCode->getCartPriceRule();

                if ($cartPriceRuleProcessor->process($cart, $priceRule, $voucherCode)) {
                    $cartManager->persistCart($cart);
                    $this->addFlash('success', $translator->trans('coreshop.ui.success.voucher.stored'));
                } else {
                    $this->addFlash('error', $translator->trans('coreshop.ui.error.voucher.invalid'));
                }
            } else {
                $this->addFlash('success', $translator->trans('coreshop.ui.cart_updated'));
            }

            $eventDispatcher->dispatch('coreshop.cart.update', new GenericEvent($cart));
            $cartManager->persistCart($cart);
        } else {
            if ($cart->getId()) {
                $cart = $orderRepository->forceFind($cart->getId());
            }
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Cart/summary.html'), [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }

    public function shipmentCalculationAction(
        Request $request,
        CartContextInterface $cartContext,
        FormFactoryInterface $formFactory,
        FactoryInterface $addressFactory,
        TaxedShippingCalculatorInterface $taxedShippingCalculator,
        CarriersResolverInterface $carriersResolver,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $cart
         */
        $cart = $cartContext->getCart();
        $form = $formFactory->create(ShippingCalculatorType::class, null, [
            'action' => $this->generateCoreShopUrl(null, 'coreshop_cart_check_shipment'),
        ]);

        $availableCarriers = [];
        $form->handleRequest($request);

        //check if there is a shipping calculation request
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->isValid()) {
            $shippingCalculatorFormData = $form->getData();

            /** @var AddressInterface $virtualAddress */
            $virtualAddress = $addressFactory->createNew();
            $virtualAddress->setCountry($shippingCalculatorFormData['country']);
            $virtualAddress->setPostcode($shippingCalculatorFormData['zip']);

            $carriers = $carriersResolver->resolveCarriers($cart, $virtualAddress);
            foreach ($carriers as $carrier) {
                $price = $taxedShippingCalculator->getPrice($carrier, $cart, $virtualAddress);
                $priceWithoutTax = $taxedShippingCalculator->getPrice($carrier, $cart, $virtualAddress, false);
                $availableCarriers[] = [
                    'name' => $carrier->getTitle(),
                    'isFreeShipping' => $price === 0,
                    'price' => $price,
                    'priceWithoutTax' => $priceWithoutTax,
                    'data' => $carrier,
                ];
            }
            uasort($availableCarriers, static function ($a, $b) {
                return $a['price'] > $b['price'];
            });
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Cart/ShipmentCalculator/_widget.html'), [
            'cart' => $cart,
            'form' => $form->createView(),
            'availableCarriers' => $availableCarriers,
        ]);
    }

    public function addItemAction(
        Request $request,
        CartContextInterface $cartContext,
        StackRepository $purchasableStackRepository,
        FactoryInterface $orderItemFactory,
        AddToCartFactoryInterface $addToCartFactory,
        FormFactoryInterface $formFactory,
        CartModifierInterface $cartModifier,
        CartManagerInterface $cartManager,
        TrackerInterface $tracker,
        TranslatorInterface $translator,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $redirect = $request->get('_redirect', $this->generateCoreShopUrl(null, 'coreshop_index'));

        $product = $purchasableStackRepository->find($request->get('product'));

        if (!$product instanceof PurchasableInterface) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                ]);
            }

            return $this->redirect($redirect);
        }

        $cartItem = $orderItemFactory->createWithPurchasable($product);

        $addToCart = $this->createAddToCart($addToCartFactory, $cartContext->getCart(), $cartItem);

        $form = $formFactory->create(AddToCartType::class, $addToCart);

        if ($request->isMethod('POST')) {
            $redirect = $request->get('_redirect',
                $this->generateCoreShopUrl($cartContext->getCart(), 'coreshop_cart_summary'));

            if ($form->handleRequest($request)->isValid()) {
                /**
                 * @var AddToCartInterface $addToCart
                 */
                $addToCart = $form->getData();

                $cartModifier->addToList($addToCart->getCart(), $addToCart->getCartItem());
                $cartManager->persistCart($cartContext->getCart());

                $tracker->trackCartAdd(
                    $addToCart->getCart(),
                    $addToCart->getCartItem()->getProduct(),
                    $addToCart->getCartItem()->getQuantity()
                );

                $this->addFlash('success', $translator->trans('coreshop.ui.item_added'));

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'success' => true,
                    ]);
                }

                return $this->redirect($redirect);
            }

            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => array_map(function (FormError $error) {
                        return $error->getMessage();
                    }, iterator_to_array($form->getErrors(true))),
                ]);
            }

            return $this->redirect($redirect);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => false,
            ]);
        }

        return $this->renderTemplate(
            $request->get('template', $templateConfigurator->findTemplate('Product/_addToCart.html')),
            [
                'form' => $form->createView(),
                'product' => $product,
            ]
        );
    }

    public function removeItemAction(
        Request $request,
        CartContextInterface $cartContext,
        OrderItemRepositoryInterface $orderItemRepository,
        TranslatorInterface $translator,
        TrackerInterface $tracker,
        CartModifierInterface $cartModifier,
        CartManagerInterface $cartManager
    ): Response {
        $cartItem = $orderItemRepository->find($request->get('cartItem'));

        if (!$cartItem instanceof OrderItemInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if ($cartItem->getOrder()->getId() !== $cartContext->getCart()->getId()) {
            return $this->redirectToRoute('coreshop_index');
        }

        $this->addFlash('success', $translator->trans('coreshop.ui.item_removed'));

        $cartModifier->removeFromList($cartContext->getCart(), $cartItem);
        $cartManager->persistCart($cartContext->getCart());

        $tracker->trackCartRemove($cartContext->getCart(), $cartItem->getProduct(), $cartItem->getQuantity());

        return $this->redirectToRoute('coreshop_cart_summary');
    }

    public function removePriceRuleAction(
        Request $request,
        CartContextInterface $cartContext,
        CartPriceRuleVoucherRepositoryInterface $cartPriceRuleVoucherCodeRepository,
        CartPriceRuleUnProcessorInterface $unProcessor,
        CartManagerInterface $cartManager
    ): Response {
        $code = $request->get('code');
        $cart = $cartContext->getCart();

        $voucherCode = $cartPriceRuleVoucherCodeRepository->findByCode($code);

        if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
            return $this->redirectToRoute('coreshop_cart_summary');
        }

        $priceRule = $voucherCode->getCartPriceRule();

        $unProcessor->unProcess($cart, $priceRule, $voucherCode);
        $cartManager->persistCart($cart);

        return $this->redirectToRoute('coreshop_cart_summary');
    }

    protected function createAddToCart(
        AddToCartFactoryInterface $addToCartFactory,
        OrderInterface $cart,
        OrderItemInterface $cartItem
    ): AddToCartInterface {
        return $addToCartFactory->createWithCartAndCartItem($cart, $cartItem);
    }
}
