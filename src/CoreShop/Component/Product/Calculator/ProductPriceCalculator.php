<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Exception\NoDiscountPriceFoundException;
use CoreShop\Component\Product\Exception\NoRetailPriceFoundException;
use CoreShop\Component\Product\Model\ProductInterface;

final class ProductPriceCalculator implements ProductPriceCalculatorInterface
{
    private ProductRetailPriceCalculatorInterface $retailPriceCalculator;
    private ProductDiscountPriceCalculatorInterface $discountPriceCalculator;
    private ProductDiscountCalculatorInterface $discountCalculator;

    public function __construct(
        ProductRetailPriceCalculatorInterface $retailPriceCalculator,
        ProductDiscountPriceCalculatorInterface $discountPriceCalculator,
        ProductDiscountCalculatorInterface $discountCalculator
    ) {
        $this->retailPriceCalculator = $retailPriceCalculator;
        $this->discountPriceCalculator = $discountPriceCalculator;
        $this->discountCalculator = $discountCalculator;
    }

    public function getPrice(ProductInterface $product, array $context, bool $includingDiscounts = false): int
    {
        $retailPrice = $this->getRetailPrice($product, $context);
        $price = $retailPrice;

        $discountPrice = $this->getDiscountPrice($product, $context);

        if ($discountPrice > 0 && $discountPrice < $price) {
            $price = $discountPrice;
        }

        if ($includingDiscounts) {
            $price -= $this->getDiscount($product, $context, $price);
        }

        return $price;
    }

    public function getRetailPrice(ProductInterface $subject, array $context): int
    {
        try {
            return $this->retailPriceCalculator->getRetailPrice($subject, $context);
        } catch (NoRetailPriceFoundException $ex) {
        }

        return 0;
    }

    public function getDiscountPrice(ProductInterface $subject, array $context): int
    {
        try {
            return $this->discountPriceCalculator->getDiscountPrice($subject, $context);
        } catch (NoDiscountPriceFoundException $ex) {
        }

        return 0;
    }

    public function getDiscount(ProductInterface $subject, array $context, int $price): int
    {
        return $this->discountCalculator->getDiscount($subject, $context, $price);
    }
}
