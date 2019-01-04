<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Cart\Rule\Applier;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Core\Provider\AddressProviderInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributor;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;

class DiscountApplier implements DiscountApplierInterface
{
    /**
     * @var ProportionalIntegerDistributor
     */
    private $distributor;

    /**
     * @var ProductTaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @var AddressProviderInterface
     */
    private $defaultAddressProvider;

    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param ProportionalIntegerDistributor       $distributor
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param TaxCollectorInterface                $taxCollector
     * @param AddressProviderInterface             $defaultAddressProvider
     * @param AdjustmentFactoryInterface           $adjustmentFactory
     */
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

    /**
     * {@inheritdoc}
     */
    public function applyDiscount(CartInterface $cart, ProposalCartPriceRuleItemInterface $cartPriceRuleItem, int $discount, $withTax = false)
    {
        $totalAmount = [];

        foreach ($cart->getItems() as $item) {
            $totalAmount[] = $item->getTotal(false);
        }

        $distributedAmount = $this->distributor->distribute($totalAmount, $discount);

        $totalDiscountNet = 0;
        $totalDiscountGross = 0;
        $i = 0;

        foreach ($cart->getItems() as $item) {
            $applicableAmount = $distributedAmount[$i++];
            $itemDiscountGross = 0;
            $itemDiscountNet = 0;

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
                    $itemDiscountNet = $applicableAmount / (1 + $taxCalculator->getTotalRate() / 100);
                } else {
                    $itemDiscountGross = $applicableAmount * (1 + ($taxCalculator->getTotalRate() / 100));
                }

                $taxItems = $item->getTaxes();
                $taxItems->setItems($this->taxCollector->collectTaxes($taxCalculator, -1 * $itemDiscountNet, $taxItems->getItems()));
            } else {
                if ($withTax) {
                    $itemDiscountNet = $applicableAmount;
                } else {
                    $itemDiscountGross = $applicableAmount;
                }
            }

            $totalDiscountNet += $itemDiscountNet;
            $totalDiscountGross += $itemDiscountGross;
        }

        $cartPriceRuleItem->setDiscount((int) round($totalDiscountNet), false);
        $cartPriceRuleItem->setDiscount((int) round($totalDiscountGross), true);

        $cart->addAdjustment(
            $this->adjustmentFactory->createWithData(
                AdjustmentInterface::CART_PRICE_RULE,
                $cartPriceRuleItem->getCartPriceRule()->getName(),
                -1 * $cartPriceRuleItem->getDiscount(true),
                -1 * $cartPriceRuleItem->getDiscount(false)
            )
        );
    }
}
