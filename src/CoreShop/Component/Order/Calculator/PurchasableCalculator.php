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

final class PurchasableCalculator implements PurchasableCalculatorInterface
{
    /**
     * @var PurchasablePriceCalculatorInterface
     */
    private $purchasablePriceCalculator;

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
     * @param PurchasablePriceCalculatorInterface $purchasablePriceCalculator
     * @param PurchasableRetailPriceCalculatorInterface $purchasableRetailPriceCalculator
     * @param PurchasableDiscountPriceCalculatorInterface $purchasableDiscountPriceCalculator
     * @param PurchasableDiscountCalculatorInterface $purchasableDiscountCalculator
     */
    public function __construct(
        PurchasablePriceCalculatorInterface $purchasablePriceCalculator,
        PurchasableRetailPriceCalculatorInterface $purchasableRetailPriceCalculator,
        PurchasableDiscountPriceCalculatorInterface $purchasableDiscountPriceCalculator,
        PurchasableDiscountCalculatorInterface $purchasableDiscountCalculator
    )
    {
        $this->purchasablePriceCalculator = $purchasablePriceCalculator;
        $this->purchasableRetailPriceCalculator = $purchasableRetailPriceCalculator;
        $this->purchasableDiscountPriceCalculator = $purchasableDiscountPriceCalculator;
        $this->purchasableDiscountCalculator = $purchasableDiscountCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $purchasable, $includingDiscounts = false)
    {
        return $this->purchasablePriceCalculator->getPrice($purchasable, $includingDiscounts);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $purchasable, $basePrice)
    {
        return $this->purchasableDiscountCalculator->getDiscount($purchasable, $basePrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $purchasable)
    {
        return $this->purchasableDiscountPriceCalculator->getDiscountPrice($purchasable);
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $purchasable)
    {
        return $this->purchasableRetailPriceCalculator->getRetailPrice($purchasable);
    }
}