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

use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Bundle\OrderBundle\Factory\AddToCartFactoryInterface;
use CoreShop\Bundle\OrderBundle\Form\Type\AddToCartType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartType;
use CoreShop\Bundle\OrderBundle\Form\Type\ShippingCalculatorType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Cart\CartModifierInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CartController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction(Request $request)
    {
        return $this->render($this->templateConfigurator->findTemplate('Cart/_widget.html'), [
            'cart' => $this->getCart(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summaryAction(Request $request)
    {
        $cart = $this->getCart();
        $form = $this->get('form.factory')->createNamed('coreshop', CartType::class, $cart);
        $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->isSubmitted() && $form->isValid()) {
            $cart = $form->getData();
            $code = $form->get('cartRuleCoupon')->getData();

            if ($code) {
                $voucherCode = $this->getCartPriceRuleVoucherRepository()->findByCode($code);

                if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
                    $this->addFlash('error', $this->get('translator')->trans('coreshop.ui.error.voucher.not_found'));

                    return $this->render($this->templateConfigurator->findTemplate('Cart/summary.html'), [
                        'cart' => $this->getCart(),
                        'form' => $form->createView(),
                    ]);
                }

                $priceRule = $voucherCode->getCartPriceRule();

                if ($this->getCartPriceRuleProcessor()->process($cart, $priceRule, $voucherCode)) {
                    $this->getCartManager()->persistCart($cart);
                    $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.success.voucher.stored'));
                } else {
                    $this->addFlash('error', $this->get('translator')->trans('coreshop.ui.error.voucher.invalid'));
                }
            } else {
                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.cart_updated'));
            }

            $this->get('event_dispatcher')->dispatch(new GenericEvent($cart), 'coreshop.cart.update');
            $this->getCartManager()->persistCart($cart);
        } else {
            if ($cart->getId()) {
                $cart = $this->get('coreshop.repository.order')->forceFind($cart->getId());
            }
        }

        return $this->render($this->templateConfigurator->findTemplate('Cart/summary.html'), [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function shipmentCalculationAction(Request $request)
    {
        $cart = $this->getCart();
        $form = $this->get('form.factory')->createNamed('coreshop', ShippingCalculatorType::class, null, [
                'action' => $this->generateCoreShopUrl(null, 'coreshop_cart_check_shipment'),
            ]);

        $availableCarriers = [];
        $form->handleRequest($request);

        //check if there is a shipping calculation request
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->isSubmitted() && $form->isValid()) {
            $shippingCalculatorFormData = $form->getData();
            $carrierPriceCalculator = $this->get(TaxedShippingCalculatorInterface::class);
            $carriersResolver = $this->get(CarriersResolverInterface::class);

            /** @var AddressInterface $virtualAddress */
            $virtualAddress = $this->get('coreshop.factory.address')->createNew();
            $virtualAddress->setCountry($shippingCalculatorFormData['country']);
            $virtualAddress->setPostcode($shippingCalculatorFormData['zip']);

            $carriers = $carriersResolver->resolveCarriers($cart, $virtualAddress);
            foreach ($carriers as $carrier) {
                $price = $carrierPriceCalculator->getPrice($carrier, $cart, $virtualAddress);
                $priceWithoutTax = $carrierPriceCalculator->getPrice($carrier, $cart, $virtualAddress, false);
                $availableCarriers[] = [
                    'name' => $carrier->getTitle(),
                    'isFreeShipping' => $price === 0,
                    'price' => $price,
                    'priceWithoutTax' => $priceWithoutTax,
                    'data' => $carrier,
                ];
            }
            uasort($availableCarriers, function ($a, $b) {
                return $a['price'] > $b['price'];
            });
        }

        return $this->render($this->templateConfigurator->findTemplate('Cart/ShipmentCalculator/_widget.html'), [
            'cart' => $cart,
            'form' => $form->createView(),
            'availableCarriers' => $availableCarriers,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addItemAction(Request $request)
    {
        $redirect = $request->get('_redirect', $this->generateCoreShopUrl(null, 'coreshop_index'));

        $product = $this->get('coreshop.repository.stack.purchasable')->find($request->get('product'));

        if (!$product instanceof PurchasableInterface) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => false,
                ]);
            }

            return $this->redirect($redirect);
        }

        $cartItem = $this->get('coreshop.factory.order_item')->createWithPurchasable($product);

        $addToCart = $this->createAddToCart($this->getCart(), $cartItem);

        $form = $this->get('form.factory')->createNamed('coreshop-' . $product->getId(), AddToCartType::class, $addToCart);

        if ($request->isMethod('POST')) {
            $redirect = $request->get('_redirect', $this->generateCoreShopUrl($this->getCart(), 'coreshop_cart_summary'));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /**
                 * @var AddToCartInterface $addToCart
                 */
                $addToCart = $form->getData();

                $this->getCartModifier()->addToList($addToCart->getCart(), $addToCart->getCartItem());
                $this->getCartManager()->persistCart($this->getCart());

                $this->get(TrackerInterface::class)->trackCartAdd(
                    $addToCart->getCart(),
                    $addToCart->getCartItem()->getProduct(),
                    $addToCart->getCartItem()->getQuantity()
                );

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_added'));

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

        return $this->render(
            $request->get('template', $this->templateConfigurator->findTemplate('Product/_addToCart.html')),
            [
                'form' => $form->createView(),
                'product' => $product,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeItemAction(Request $request)
    {
        $cartItem = $this->get('coreshop.repository.order_item')->find($request->get('cartItem'));

        if (!$cartItem instanceof OrderItemInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if ($cartItem->getOrder()->getId() !== $this->getCart()->getId()) {
            return $this->redirectToRoute('coreshop_index');
        }

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.item_removed'));

        $this->getCartModifier()->removeFromList($this->getCart(), $cartItem);
        $this->getCartManager()->persistCart($this->getCart());

        $this->get(TrackerInterface::class)->trackCartRemove($this->getCart(), $cartItem->getProduct(), $cartItem->getQuantity());

        return $this->redirectToRoute('coreshop_cart_summary');
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removePriceRuleAction(Request $request)
    {
        $code = $request->get('code');
        $cart = $this->getCart();

        $voucherCode = $this->getCartPriceRuleVoucherRepository()->findByCode($code);

        if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
            return $this->redirectToRoute('coreshop_cart_summary');
        }

        $priceRule = $voucherCode->getCartPriceRule();

        $this->getCartPriceRuleUnProcessor()->unProcess($cart, $priceRule, $voucherCode);
        $this->getCartManager()->persistCart($cart);

        return $this->redirectToRoute('coreshop_cart_summary');
    }

    /**
     * @param OrderInterface     $cart
     * @param OrderItemInterface $cartItem
     *
     * @return AddToCartInterface
     */
    protected function createAddToCart(OrderInterface $cart, OrderItemInterface $cartItem)
    {
        return $this->get(AddToCartFactoryInterface::class)->createWithCartAndCartItem($cart, $cartItem);
    }

    /**
     * @return CartPriceRuleProcessorInterface
     */
    protected function getCartPriceRuleProcessor()
    {
        return $this->get(CartPriceRuleProcessorInterface::class);
    }

    /**
     * @return CartPriceRuleUnProcessorInterface
     */
    protected function getCartPriceRuleUnProcessor()
    {
        return $this->get(CartPriceRuleUnProcessorInterface::class);
    }

    /**
     * @return StorageListModifierInterface
     */
    protected function getCartModifier()
    {
        return $this->get(CartModifierInterface::class);
    }

    /**
     * @return CartPriceRuleVoucherRepositoryInterface
     */
    protected function getCartPriceRuleVoucherRepository()
    {
        return $this->get('coreshop.repository.cart_price_rule_voucher_code');
    }

    /**
     * @return \CoreShop\Component\Order\Model\OrderInterface
     */
    protected function getCart()
    {
        return $this->getCartContext()->getCart();
    }

    /**
     * @return CartContextInterface
     */
    protected function getCartContext()
    {
        return $this->get(CartContextInterface::class);
    }

    /**
     * @return CartManagerInterface
     */
    protected function getCartManager()
    {
        return $this->get(CartManagerInterface::class);
    }

    /**
     * @param OrderItemInterface $cartItem
     *
     * @return ConstraintViolationListInterface
     */
    private function getCartItemErrors(OrderItemInterface $cartItem)
    {
        return $this
            ->get('validator')
            ->validate($cartItem, null, $this->container->getParameter('coreshop.form.type.cart_item.validation_groups'));
    }
}
