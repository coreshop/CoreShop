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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;

final class ProductPriceCalculator implements ProductPriceCalculatorInterface
{
    /**
     * @var ProductRetailPriceCalculatorInterface
     */
    private $retailPriceCalculator;

    /**
     * @var ProductDiscountPriceCalculatorInterface
     */
    private $discountPriceCalculator;

    /**
     * @var ProductDiscountCalculatorInterface
     */
    private $discountCalculator;

    /**
     * @param ProductRetailPriceCalculatorInterface   $retailPriceCalculator
     * @param ProductDiscountPriceCalculatorInterface $discountPriceCalculator
     * @param ProductDiscountCalculatorInterface      $discountCalculator
     */
    public function __construct(
        ProductRetailPriceCalculatorInterface $retailPriceCalculator,
        ProductDiscountPriceCalculatorInterface $discountPriceCalculator,
        ProductDiscountCalculatorInterface $discountCalculator
    ) {
        $this->retailPriceCalculator = $retailPriceCalculator;
        $this->discountPriceCalculator = $discountPriceCalculator;
        $this->discountCalculator = $discountCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(ProductInterface $product, array $context, $includingDiscounts = false)
    {
        $retailPrice = $this->getRetailPrice($product, $context);
        $discountPrice = $this->getDiscountPrice($product, $context);
        $price = $retailPrice;

        if ($discountPrice > 0 && $discountPrice < $retailPrice) {
            $price = $discountPrice;
        }

        if ($includingDiscounts) {
            $price -= $this->getDiscount($product, $context, $price);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(ProductInterface $subject, array $context)
    {
        return $this->retailPriceCalculator->getRetailPrice($subject, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(ProductInterface $subject, array $context)
    {
        return $this->discountPriceCalculator->getDiscountPrice($subject, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(ProductInterface $subject, array $context, $price)
    {
        return $this->discountCalculator->getDiscount($subject, $context, $price);
    }
}
