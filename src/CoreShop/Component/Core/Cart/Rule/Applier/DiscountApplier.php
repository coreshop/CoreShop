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

namespace CoreShop\Component\Core\Cart\Rule\Applier;

use CoreShop\Component\Core\Product\ProductTaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Distributor\ProportionalIntegerDistributor;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class DiscountApplier
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
     * @param ProportionalIntegerDistributor       $distributor
     * @param ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
     */
    public function __construct(
        ProportionalIntegerDistributor $distributor,
        ProductTaxCalculatorFactoryInterface $taxCalculatorFactory
    ) {
        $this->distributor = $distributor;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
    }

    public function applyDiscount(CartInterface $cart, ProposalCartPriceRuleItemInterface $cartPriceRuleItem, int $discount, $withTax = false)
    {
        $totalAmount = [];

        foreach ($cart->getItems() as $item) {
            $totalAmount[] = $item->getTotal($withTax);
        }

        $distributedAmount = $this->distributor->distribute($totalAmount, $discount);

        $totalDiscountNet = 0;
        $totalDiscountGross = 0;
        $i = 0;

        foreach ($cart->getItems() as $item) {
            $applicableAmount = $distributedAmount[$i++];

            if (0 === $applicableAmount) {
                continue;
            }

            $item->setDiscount($applicableAmount, $withTax);

            $taxCalculator = $this->taxCalculatorFactory->getTaxCalculator(
                $item->getProduct(),
                $cart->getShippingAddress()
            );

            if ($taxCalculator instanceof TaxCalculatorInterface) {
                if ($withTax) {
                    $item->setDiscount($taxCalculator->removeTaxes($applicableAmount), !$withTax);
                } else {
                    $item->setDiscount($taxCalculator->applyTaxes($applicableAmount), !$withTax);
                }
            }
            else {
                $item->setDiscount($item->getDiscount($withTax), !$withTax);
            }

            $totalDiscountNet += $item->getDiscount(false);
            $totalDiscountGross += $item->getDiscount(true);
        }

        $cartPriceRuleItem->setDiscount($totalDiscountNet, false);
        $cartPriceRuleItem->setDiscount($totalDiscountGross, true);
    }
}
