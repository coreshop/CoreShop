<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\OrderBundle\Form\Type\CartType;
use CoreShop\Bundle\OrderBundle\Form\Type\ShippingCalculatorType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\StorageList\StorageListModifierInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction(Request $request)
    {
        return $this->renderTemplate($this->templateConfigurator->findTemplate('Cart/_widget.html'), [
            'cart' => $this->getCart(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summaryAction(Request $request)
    {
        $cart = $this->getCart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        $view = View::create($cart)
            ->setTemplate($this->templateConfigurator->findTemplate('Cart/summary.html'))
            ->setTemplateData([
                'cart' => $cart,
                'form' => $form->createView()
            ]);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->isValid()) {
            $cart = $form->getData();
            $code = $form->get('cartRuleCoupon')->getData();

            if ($code) {
                $voucherCode = $this->getCartPriceRuleVoucherRepository()->findByCode($code);

                if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
                    $this->addFlash('error', 'coreshop.ui.error.voucher.not_found');

                    return $this->viewHandler->handle($view);
                }

                // TODO: would be better to do that inside the form
                foreach ($this->getCart()->getPriceRules() as $rule) {
                    if (!$rule instanceof CartPriceRuleInterface) {
                        continue;
                    }

                    if ($rule->getId() === $voucherCode->getCartPriceRule()->getId()) {
                        $this->addFlash('error', 'coreshop.ui.error.voucher.invalid');

                        return $this->viewHandler->handle($view);
                    }
                }

                $priceRule = $voucherCode->getCartPriceRule();

                if ($this->getCartPriceRuleProcessor()->process($cart, $priceRule, $voucherCode)) {
                    $this->getCartManager()->persistCart($cart);
                    $this->addFlash('success', 'coreshop.ui.success.voucher.stored');
                } else {
                    $this->addFlash('error', 'coreshop.ui.error.voucher.invalid');
                }
            } else {
                $this->addFlash('success', 'coreshop.ui.cart_updated');
            }

            $this->get('event_dispatcher')->dispatch('coreshop.cart.update', new GenericEvent($cart));
            $this->getCartManager()->persistCart($cart);
        } else {
            if ($cart->getId()) {
                $view->setTemplateData([
                    'cart' => $this->get('coreshop.repository.cart')->forceFind($cart->getId()),
                    'form' => $form->createView()
                ]);
            }
        }

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function shipmentCalculationAction(Request $request)
    {
        $cart = $this->getCart();
        $form = $this->createForm(ShippingCalculatorType::class, null, [
                'action' => $this->generateUrl('coreshop_cart_check_shipment')
            ]
        );

        $availableCarriers = [];
        $form->handleRequest($request);

        //check if there is a shipping calculation request
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->isValid()) {

            $shippingCalculatorFormData = $form->getData();
            $carrierPriceCalculator = $this->get('coreshop.carrier.price_calculator.taxed');
            $carriersResolver = $this->get('coreshop.carrier.resolver');

            /** @var AddressInterface $virtualAddress */
            $virtualAddress = $this->get('coreshop.factory.address')->createNew();
            $virtualAddress->setCountry($shippingCalculatorFormData['country']);
            $virtualAddress->setPostcode($shippingCalculatorFormData['zip']);

            $carriers = $carriersResolver->resolveCarriers($cart, $virtualAddress);
            foreach ($carriers as $carrier) {
                $price = $carrierPriceCalculator->getPrice($carrier, $cart, $virtualAddress);
                $priceWithoutTax = $carrierPriceCalculator->getPrice($carrier, $cart, $virtualAddress, false);
                $availableCarriers[] = [
                    'name' => $carrier->getLabel(),
                    'isFreeShipping' => $price === 0,
                    'price' => $price,
                    'priceWithoutTax' => $priceWithoutTax,
                    'data' => $carrier
                ];
            }
            uasort($availableCarriers, function ($a, $b) {
                return ($a['price'] > $b['price']);
            });
        }

        $view = View::create($cart)
            ->setTemplate($this->templateConfigurator->findTemplate('Cart/ShipmentCalculator/_widget.html'))
            ->setTemplateVar('cart')
            ->setTemplateData([
                'cart' => $cart,
                'form' => $form->createView(),
                'availableCarriers' => $availableCarriers
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addItemAction(Request $request)
    {
        $product = $this->get('coreshop.repository.stack.purchasable')->find($request->get('product'));

        if (!$product instanceof PurchasableInterface) {
            throw new NotFoundHttpException();
        }

        $quantity = intval($request->get('quantity', 1));

        if (!is_int($quantity)) {
            $quantity = 1;
        }

        if ($product instanceof StockableInterface) {
            $hasStock = $this->get('coreshop.inventory.availability_checker.default')->isStockSufficient($product, $quantity);

            if (!$hasStock) {
                $this->addFlash('error', 'coreshop.ui.item_is_out_of_stock');
                return $this->viewHandler->handle(View::createRouteRedirect('coreshop_cart_summary'));
            }
        }

        $this->getCartModifier()->addItem($this->getCart(), $product, $quantity);
        $this->getCartManager()->persistCart($this->getCart());

        $this->addFlash('success', 'coreshop.ui.item_added');

        return $this->viewHandler->handle(View::createRouteRedirect('coreshop_cart_summary'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeItemAction(Request $request)
    {
        $cartItem = $this->get('coreshop.repository.cart_item')->find($request->get('cartItem'));

        if (!$cartItem instanceof CartItemInterface) {
            throw new NotFoundHttpException();
        }

        if ($cartItem->getCart()->getId() !== $this->getCart()->getId()) {
            throw new AccessDeniedHttpException();
        }

        $this->addFlash('success', 'coreshop.ui.item_removed');

        $this->getCartModifier()->removeItem($this->getCart(), $cartItem);
        $this->getCartManager()->persistCart($this->getCart());

        return $this->viewHandler->handle(View::createRouteRedirect('coreshop_cart_summary'));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removePriceRuleAction(Request $request)
    {
        $code = $request->get('code');
        $cart = $this->getCart();

        $voucherCode = $this->getCartPriceRuleVoucherRepository()->findByCode($code);

        if (!$voucherCode instanceof CartPriceRuleVoucherCodeInterface) {
            return $this->viewHandler->handle(View::createRouteRedirect('coreshop_cart_summary'));
        }

        $priceRule = $voucherCode->getCartPriceRule();

        $this->getCartPriceRuleUnProcessor()->unProcess($cart, $priceRule, $voucherCode);
        $this->getCartManager()->persistCart($cart);

        return $this->viewHandler->handle(View::createRouteRedirect('coreshop_cart_summary'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createQuoteAction(Request $request)
    {
        $quote = $this->getQuoteFactory()->createNew();
        $quote = $this->getCartToQuoteTransformer()->transform($this->getCart(), $quote);

        return $this->viewHandler->handle(View::createRouteRedirect('coreshop_quote_detail', ['quote' => $quote->getId()]));
    }

    /**
     * @return \CoreShop\Component\Resource\Factory\PimcoreFactory
     */
    protected function getQuoteFactory()
    {
        return $this->get('coreshop.factory.quote');
    }

    protected function getCartToQuoteTransformer()
    {
        return $this->get('coreshop.order.transformer.cart_to_quote');
    }

    /**
     * @return CartPriceRuleProcessorInterface
     */
    protected function getCartPriceRuleProcessor()
    {
        return $this->get('coreshop.cart_price_rule.processor');
    }

    /**
     * @return CartPriceRuleUnProcessorInterface
     */
    protected function getCartPriceRuleUnProcessor()
    {
        return $this->get('coreshop.cart_price_rule.un_processor');
    }

    /**
     * @return StorageListModifierInterface
     */
    protected function getCartModifier()
    {
        return $this->get('coreshop.cart.modifier');
    }

    /**
     * @return CartPriceRuleVoucherRepositoryInterface
     */
    protected function getCartPriceRuleVoucherRepository()
    {
        return $this->get('coreshop.repository.cart_price_rule_voucher_code');
    }

    /**
     * @return \CoreShop\Component\Order\Model\CartInterface
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
        return $this->get('coreshop.context.cart');
    }

    /**
     * @return CartManagerInterface
     */
    protected function getCartManager()
    {
        return $this->get('coreshop.cart.manager');
    }
}
