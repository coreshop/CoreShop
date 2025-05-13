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

use CoreShop\Component\Order\Exception\NoPurchasableWholesalePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableWholesalePriceCalculator implements PurchasableWholesalePriceCalculatorInterface
{
    public function __construct(
        protected PrioritizedServiceRegistryInterface $wholesalePriceCalculators,
    ) {
    }

    public function getPurchasableWholesalePrice(PurchasableInterface $purchasable, array $context): int
    {
        $price = null;

        /**
         * @var PurchasableWholesalePriceCalculatorInterface $calculator
         */
        foreach ($this->wholesalePriceCalculators->all() as $calculator) {
            try {
                $actionPrice = $calculator->getPurchasableWholesalePrice($purchasable, $context);
                $price = $actionPrice;
            } catch (NoPurchasableWholesalePriceFoundException) {
            }
        }

        if (null === $price) {
            throw new NoPurchasableWholesalePriceFoundException(__CLASS__);
        }

        return $price;
    }
}
