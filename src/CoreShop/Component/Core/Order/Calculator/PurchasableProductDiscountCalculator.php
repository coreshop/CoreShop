<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Order\Calculator;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Calculator\PurchasableDiscountCalculatorInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;

final class PurchasableProductDiscountCalculator implements PurchasableDiscountCalculatorInterface
{
    public function __construct(
        private ProductPriceCalculatorInterface $productPriceCalculator,
    ) {
    }

    public function getDiscount(PurchasableInterface $purchasable, array $context, int $basePrice): int
    {
        if ($purchasable instanceof ProductInterface) {
            return $this->productPriceCalculator->getDiscount($purchasable, $context, $basePrice);
        }

        return 0;
    }
}
