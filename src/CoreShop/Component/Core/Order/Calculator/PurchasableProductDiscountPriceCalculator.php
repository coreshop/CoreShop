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

namespace CoreShop\Component\Core\Order\Calculator;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;

final class PurchasableProductDiscountPriceCalculator implements PurchasableDiscountPriceCalculatorInterface
{
    /**
     * @var ProductPriceCalculatorInterface
     */
    private $productPriceCalculator;

    /**
     * @param ProductPriceCalculatorInterface $productPriceCalculator
     */
    public function __construct(ProductPriceCalculatorInterface $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountPrice(PurchasableInterface $purchasable, array $context)
    {
        if ($purchasable instanceof ProductInterface) {
            $discount = $this->productPriceCalculator->getDiscountPrice($purchasable, $context);

            return $discount;
        }

        return 0;
    }
}
