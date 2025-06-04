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
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Exception\NoPurchasablePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Exception\NoPriceFoundException;

final class PurchasableProductPriceCalculator implements PurchasablePriceCalculatorInterface
{
    public function __construct(
        private ProductPriceCalculatorInterface $productPriceCalculator,
    ) {
    }

    public function getPrice(PurchasableInterface $purchasable, array $context, bool $includingDiscounts = false): int
    {
        if ($purchasable instanceof ProductInterface) {
            try {
                return $this->productPriceCalculator->getPrice($purchasable, $context, $includingDiscounts);
            } catch (NoPriceFoundException) {
            }
        }

        throw new NoPurchasablePriceFoundException(__CLASS__);
    }
}
