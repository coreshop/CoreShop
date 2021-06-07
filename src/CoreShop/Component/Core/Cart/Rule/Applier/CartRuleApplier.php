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

namespace CoreShop\Component\Core\Cart\Rule\Applier;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributor;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;

class CartRuleApplier implements CartRuleApplierInterface
{
    private $distributor;
    private $taxCalculatorFactory;
    private $taxCollector;
    private $defaultAddressProvider;
    private $adjustmentFactory;

    public function __construct(
        ProportionalIntegerDistributor $distributor,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory,
        TaxCollectorInterface $taxCollector,
        AddressProviderInterface $defaultAddressProvider,
        AdjustmentFactoryInterface $adjustmentFactory
    ) {
        $this->distributor = $distributor;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->taxCollector = $taxCollector;
        $this->defaultAddressProvider = $defaultAddressProvider;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public function applyDiscount(OrderInterface $cart, ProposalCartPriceRuleItemInterface $cartPriceRuleItem, int $discount, bool $withTax = false): void
    {
        $this->apply($cart, $cartPriceRuleItem, $discount, $withTax, false);
    }

    public function applySurcharge(OrderInterface $cart, ProposalCartPriceRuleItemInterface $cartPriceRuleItem, int $discount, bool $withTax = false): void
    {
        $this->apply($cart, $cartPriceRuleItem, $discount, $withTax, true);
    }

    protected function apply(OrderInterface $cart, ProposalCartPriceRuleItemInterface $cartPriceRuleItem, int $discount, $withTax = false, $positive = false): void
    {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $cart
         */
        $totalAmount = [];

        foreach ($cart->getItems() as $item) {
            $totalAmount[] = $item->getTotal(false);
        }

        $distributedAmount = $this->distributor->distribute($totalAmount, $discount);

        $totalDiscountFloat = 0;
        $totalDiscountNet = 0;
        $totalDiscountGross = 0;
        $i = 0;

        foreach ($cart->getItems() as $item) {
            $applicableAmount = $distributedAmount[$i++];
            $itemDiscountGross = 0;
            $itemDiscountNet = 0;
            $discountFloat = 0;

            if (0 === $applicableAmount) {
                continue;
            }

            if ($withTax) {
                $itemDiscountGross = $applicableAmount;
            } else {
                $itemDiscountNet = $applicableAmount;
            }

            $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator(
                $item->getProduct(),
                $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart)
            );

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                if ($withTax) {
                    $discountFloat = $applicableAmount / (1 + $taxCalculator->getTotalRate() / 100);
                    $itemDiscountNet = (int)$discountFloat;
                } else {
                    $discountFloat = $applicableAmount * (1 + ($taxCalculator->getTotalRate() / 100));
                    $itemDiscountGross = (int)$discountFloat;
                }
            } else {
                if ($withTax) {
                    $itemDiscountNet = $applicableAmount;
                    $discountFloat = $applicableAmount;
                } else {
                    $itemDiscountGross = $applicableAmount;
                    $discountFloat = $applicableAmount;
                }
            }

            $totalDiscountFloat += $discountFloat;
            $totalDiscountNet += $itemDiscountNet;
            $totalDiscountGross += $itemDiscountGross;
        }

        $totalDiscountNet = (int) round($totalDiscountNet);
        $totalDiscountGross = (int) round($totalDiscountGross);
        $totalDiscountFloat = (int) round($totalDiscountFloat);

        //Add missing cents caused by rounding issues
        if ($totalDiscountFloat > ($withTax ? $totalDiscountNet : $totalDiscountGross)) {
            if ($withTax) {
                $totalDiscountNet += (int)$totalDiscountFloat - $totalDiscountNet;
            }
            else {
                $totalDiscountGross += (int)$totalDiscountFloat - $totalDiscountGross;
            }
        }

        $totalAmountNet = [];
        $totalAmountGross = [];

        foreach ($cart->getItems() as $item) {
            $totalAmountNet[] = $item->getTotal(false);
            $totalAmountGross[] = $item->getTotal(true);
        }

        $distributedAmountNet = $this->distributor->distribute($totalAmountNet, $totalDiscountNet);
        $distributedAmountGross = $this->distributor->distribute($totalAmountGross, $totalDiscountGross);

        foreach ($cart->getItems() as $index => $item) {
            $amountNet = $distributedAmountNet[$index];
            $amountGross = $distributedAmountGross[$index];

            if ($amountNet === 0) {
                continue;
            }

             $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator(
                $item->getProduct(),
                $cart->getShippingAddress() ?: $this->defaultAddressProvider->getAddress($cart)
            );

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                $taxItems = $item->getTaxes();

                if ($withTax) {
                    $taxItems->setItems(
                        $this->taxCollector->collectTaxesFromGross(
                            $taxCalculator,
                            ($positive ? $amountGross : -1 * $amountGross),
                            $taxItems->getItems()
                        )
                    );
                } else {
                    $taxItems->setItems(
                        $this->taxCollector->collectTaxes(
                            $taxCalculator,
                            ($positive ? $amountNet : -1 * $amountNet),
                            $taxItems->getItems()
                        )
                    );
                }
            }

            $item->addAdjustment($this->adjustmentFactory->createWithData(
                AdjustmentInterface::CART_PRICE_RULE,
                $cartPriceRuleItem->getCartPriceRule()->getName(),
                $positive ? $amountGross : (-1 * $amountGross),
                $positive ? $amountNet : (-1 * $amountNet),
                true
            ));
        }

        $cartPriceRuleItem->setDiscount($positive ? $totalDiscountNet : (-1 * $totalDiscountNet), false);
        $cartPriceRuleItem->setDiscount($positive ? $totalDiscountGross : (-1 * $totalDiscountGross), true);

        $cart->addAdjustment(
            $this->adjustmentFactory->createWithData(
                AdjustmentInterface::CART_PRICE_RULE,
                $cartPriceRuleItem->getCartPriceRule()->getName(),
                $cartPriceRuleItem->getDiscount(true),
                $cartPriceRuleItem->getDiscount(false)
            )
        );
    }
}
