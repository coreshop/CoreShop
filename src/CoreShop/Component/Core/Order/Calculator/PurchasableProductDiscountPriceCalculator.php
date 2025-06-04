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
use CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface;
use CoreShop\Component\Order\Exception\NoPurchasableDiscountPriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Exception\NoDiscountPriceFoundException;

final class PurchasableProductDiscountPriceCalculator implements PurchasableDiscountPriceCalculatorInterface
{
    public function __construct(
        private ProductPriceCalculatorInterface $productPriceCalculator,
    ) {
    }

    public function getDiscountPrice(PurchasableInterface $purchasable, array $context): int
    {
        if ($purchasable instanceof ProductInterface) {
            try {
                return $this->productPriceCalculator->getDiscountPrice($purchasable, $context);
            } catch (NoDiscountPriceFoundException) {
            }
        }

        throw new NoPurchasableDiscountPriceFoundException(__CLASS__);
    }
}
