<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Order\Calculator;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface;
use CoreShop\Component\Order\Exception\NoPurchasableDiscountPriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Exception\NoDiscountPriceFoundException;

final class PurchasableProductDiscountPriceCalculator implements PurchasableDiscountPriceCalculatorInterface
{
    private ProductPriceCalculatorInterface $productPriceCalculator;

    public function __construct(ProductPriceCalculatorInterface $productPriceCalculator)
    {
        $this->productPriceCalculator = $productPriceCalculator;
    }

    public function getDiscountPrice(PurchasableInterface $purchasable, array $context): int
    {
        if ($purchasable instanceof ProductInterface) {
            try {
                return $this->productPriceCalculator->getDiscountPrice($purchasable, $context);
            } catch (NoDiscountPriceFoundException $ex) {
            }
        }

        throw new NoPurchasableDiscountPriceFoundException(__CLASS__);
    }
}
