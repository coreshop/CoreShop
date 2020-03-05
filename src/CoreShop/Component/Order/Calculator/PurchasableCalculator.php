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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\ORder\Exception\NoPurchasableDiscountPriceFoundException;
use CoreShop\Component\Order\Exception\NoPurchasablePriceFoundException;
use CoreShop\Component\Order\Exception\NoPurchasableRetailPriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;

final class PurchasableCalculator implements PurchasableCalculatorInterface
{
    private $purchasablePriceCalculator;
    private $purchasableRetailPriceCalculator;
    private $purchasableDiscountPriceCalculator;
    private $purchasableDiscountCalculator;

    public function __construct(
        PurchasablePriceCalculatorInterface $purchasablePriceCalculator,
        PurchasableRetailPriceCalculatorInterface $purchasableRetailPriceCalculator,
        PurchasableDiscountPriceCalculatorInterface $purchasableDiscountPriceCalculator,
        PurchasableDiscountCalculatorInterface $purchasableDiscountCalculator
    ) {
        $this->purchasablePriceCalculator = $purchasablePriceCalculator;
        $this->purchasableRetailPriceCalculator = $purchasableRetailPriceCalculator;
        $this->purchasableDiscountPriceCalculator = $purchasableDiscountPriceCalculator;
        $this->purchasableDiscountCalculator = $purchasableDiscountCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(PurchasableInterface $purchasable, array $context, bool $includingDiscounts = false): int
    {
        try {
            return $this->purchasablePriceCalculator->getPrice($purchasable, $context, $includingDiscounts);
        } catch (NoPurchasablePriceFoundException $ex) {
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $purchasable, array $context, int $basePrice): int
    {
        return $this->purchasableDiscountCalculator->getDiscount($purchasable, $context, $basePrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $purchasable, array $context): int
    {
        try {
            return $this->purchasableDiscountPriceCalculator->getDiscountPrice($purchasable, $context);
        } catch (NoPurchasableDiscountPriceFoundException $ex) {
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(PurchasableInterface $purchasable, array $context): int
    {
        try {
            return $this->purchasableRetailPriceCalculator->getRetailPrice($purchasable, $context);
        } catch (NoPurchasableRetailPriceFoundException $ex) {
        }

        return 0;
    }
}
