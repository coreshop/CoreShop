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

use CoreShop\Component\Order\Exception\NoPurchasablePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasablePriceCalculator implements PurchasablePriceCalculatorInterface
{
    public function __construct(
        protected PrioritizedServiceRegistryInterface $calculators,
    ) {
    }

    public function getPrice(PurchasableInterface $purchasable, array $context, bool $includingDiscounts = false): int
    {
        $price = null;

        /**
         * @var PurchasablePriceCalculatorInterface $calculator
         */
        foreach ($this->calculators->all() as $calculator) {
            try {
                $actionPrice = $calculator->getPrice($purchasable, $context, $includingDiscounts);
                $price = $actionPrice;
            } catch (NoPurchasablePriceFoundException) {
            }
        }

        if (null === $price) {
            throw new NoPurchasablePriceFoundException(__CLASS__);
        }

        return $price;
    }
}
