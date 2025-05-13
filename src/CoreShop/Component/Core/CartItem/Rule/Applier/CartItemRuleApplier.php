<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\CartItem\Rule\Applier;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

class CartItemRuleApplier implements CartItemRuleApplierInterface
{
    public function __construct(
        private ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        private TaxCollectorInterface $taxCollector,
        private AddressProviderInterface $defaultAddressProvider,
        private AdjustmentFactoryInterface $adjustmentFactory,
        private CartContextResolverInterface $cartContextResolver,
    ) {
    }

    public function applyDiscount(
        OrderItemInterface $orderItem,
        PriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        bool $withTax = false,
    ): void {
        $this->apply($orderItem, $cartPriceRuleItem, $discount, $withTax, false);
    }

    public function applySurcharge(
        OrderItemInterface $orderItem,
        PriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        bool $withTax = false,
    ): void {
        $this->apply($orderItem, $cartPriceRuleItem, $discount, $withTax, true);
    }

    protected function apply(
        OrderItemInterface $orderItem,
        PriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        $withTax = false,
        $positive = false,
    ): void {
        $order = $orderItem->getOrder();
        $context = $this->cartContextResolver->resolveCartContext($order);

        $totalDiscountPossible = $orderItem->getTotal();

        $discount = min($discount, $totalDiscountPossible);

        /*
         * Cart Item Rules are always applied to the cart-item, but as neutral adjustments
         * So to know how much our "virtual" total is, we need to get these cart-price rules values
         * otherwise, we might end up with a negative total
         */
        if ($withTax) {
            $total = $orderItem->getTotal() + $orderItem->getNeutralAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE);
        } else {
            $total = $orderItem->getTotal(false) + $orderItem->getNeutralAdjustmentsTotal(AdjustmentInterface::CART_PRICE_RULE, false);
        }

        $discount = min($discount, $total);

        if (0 === $discount) {
            return;
        }

        $itemDiscountGross = 0;
        $itemDiscountNet = 0;

        if ($withTax) {
            $itemDiscountGross = $discount;
        } else {
            $itemDiscountNet = $discount;
        }

        $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator(
            $orderItem->getProduct(),
            $order->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($order),
            $context,
        );

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            if ($withTax) {
                $discountFloat = $discount / (1 + $taxCalculator->getTotalRate() / 100);
                $itemDiscountNet = $discountFloat;
            } else {
                $discountFloat = $discount * (1 + ($taxCalculator->getTotalRate() / 100));
                $itemDiscountGross = $discountFloat;
            }
        } else {
            if ($withTax) {
                $itemDiscountNet = $discount;
                $discountFloat = $discount;
            } else {
                $itemDiscountGross = $discount;
                $discountFloat = $discount;
            }
        }

        $itemDiscountNet = (int) round($itemDiscountNet);
        $itemDiscountGross = (int) round($itemDiscountGross);
        $discountFloat = (int) round($discountFloat);

        //Add missing cents caused by rounding issues
        if ($discountFloat > ($withTax ? $itemDiscountNet : $itemDiscountGross)) {
            if ($withTax) {
                $itemDiscountNet += $discountFloat - $itemDiscountNet;
            } else {
                $itemDiscountGross += $discountFloat - $itemDiscountGross;
            }
        }

        $amountNet = $itemDiscountNet;
        $amountGross = $itemDiscountGross;

        if ($taxCalculator instanceof TaxCalculatorInterface) {
            $taxItems = $orderItem->getTaxes() ?? new Fieldcollection();

            if ($withTax) {
                /** @psalm-suppress InvalidArgument */
                $taxItems->setItems(
                    $this->taxCollector->collectTaxesFromGross(
                        $taxCalculator,
                        ($positive ? $amountGross : -1 * $amountGross),
                        $taxItems->getItems(),
                    ),
                );
            } else {
                /** @psalm-suppress InvalidArgument */
                $taxItems->setItems(
                    $this->taxCollector->collectTaxes(
                        $taxCalculator,
                        ($positive ? $amountNet : -1 * $amountNet),
                        $taxItems->getItems(),
                    ),
                );
            }
        }

        $orderItem->addAdjustment(
            $this->adjustmentFactory->createWithData(
                AdjustmentInterface::CART_PRICE_RULE,
                $cartPriceRuleItem->getCartPriceRule()->getName(),
                $positive ? $amountGross : (-1 * $amountGross),
                $positive ? $amountNet : (-1 * $amountNet),
            ),
        );

        $cartPriceRuleItem->setDiscount($positive ? $itemDiscountNet : (-1 * $itemDiscountNet), false);
        $cartPriceRuleItem->setDiscount($positive ? $itemDiscountGross : (-1 * $itemDiscountGross), true);
    }
}
