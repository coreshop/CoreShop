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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Model\PurchasableInterface;

final class DefaultPurchasablePriceCalculator implements PurchasablePriceCalculatorInterface
{
    /**
     * @var PurchasableRetailPriceCalculatorInterface
     */
    private $purchasableRetailPriceCalculator;

    /**
     * @var PurchasableDiscountPriceCalculatorInterface
     */
    private $purchasableDiscountPriceCalculator;

    /**
     * @var PurchasableDiscountCalculatorInterface
     */
    private $purchasableDiscountCalculator;

    /**
     * @param PurchasableRetailPriceCalculatorInterface   $purchasableRetailPriceCalculator
     * @param PurchasableDiscountPriceCalculatorInterface $purchasableDiscountPriceCalculator
     * @param PurchasableDiscountCalculatorInterface      $purchasableDiscountCalculator
     */
    public function __construct(
        PurchasableRetailPriceCalculatorInterface $purchasableRetailPriceCalculator,
        PurchasableDiscountPriceCalculatorInterface $purchasableDiscountPriceCalculator,
        PurchasableDiscountCalculatorInterface $purchasableDiscountCalculator
    ) {
        $this->purchasableRetailPriceCalculator = $purchasableRetailPriceCalculator;
        $this->purchasableDiscountPriceCalculator = $purchasableDiscountPriceCalculator;
        $this->purchasableDiscountCalculator = $purchasableDiscountCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $purchasable, $includingDiscounts = false)
    {
        $retailPrice = $this->purchasableRetailPriceCalculator->getRetailPrice($purchasable);
        $discountPrice = $this->purchasableDiscountPriceCalculator->getDiscountPrice($purchasable);
        $price = $retailPrice;

        if ($discountPrice > 0 && $discountPrice < $retailPrice) {
            $price = $discountPrice;
        }

        if ($includingDiscounts) {
            $price -= $this->purchasableDiscountCalculator->getDiscount($purchasable, $price);
        }

        return $price;
    }
}
