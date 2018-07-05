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
    public function getPrice(ProductInterface $product, $includingDiscounts = false)
    {
        $retailPrice = $this->getRetailPrice($product);
        $discountPrice = $this->getDiscountPrice($product);
        $price = $retailPrice;

        if ($discountPrice > 0 && $discountPrice < $retailPrice) {
            $price = $discountPrice;
        }

        if ($includingDiscounts) {
            $price -= $this->getDiscount($product, $price);
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getRetailPrice(ProductInterface $subject)
    {
        return $this->retailPriceCalculator->getRetailPrice($subject);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(ProductInterface $subject)
    {
        return $this->discountPriceCalculator->getDiscountPrice($subject);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(ProductInterface $subject, $price)
    {
        return $this->discountCalculator->getDiscount($subject, $price);
    }
}
