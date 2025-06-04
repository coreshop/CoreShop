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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableDiscountCalculator implements PurchasableDiscountCalculatorInterface
{
    public function __construct(
        protected PrioritizedServiceRegistryInterface $discountCalculators,
    ) {
    }

    public function getDiscount(PurchasableInterface $purchasable, array $context, int $basePrice): int
    {
        $discounts = 0;

        /**
         * @var PurchasableDiscountCalculatorInterface $calculator
         */
        foreach ($this->discountCalculators->all() as $calculator) {
            $discounts += $calculator->getDiscount($purchasable, $context, $basePrice);
        }

        return $discounts;
    }
}
