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

use CoreShop\Component\Order\Exception\NoPurchasableRetailPriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableRetailPriceCalculator implements PurchasableRetailPriceCalculatorInterface
{
    public function __construct(
        protected PrioritizedServiceRegistryInterface $calculators,
    ) {
    }

    public function getRetailPrice(PurchasableInterface $purchasable, array $context): int
    {
        $price = null;

        /**
         * @var PurchasableRetailPriceCalculatorInterface $calculator
         */
        foreach ($this->calculators->all() as $calculator) {
            try {
                $actionPrice = $calculator->getRetailPrice($purchasable, $context);
                $price = $actionPrice;
            } catch (NoPurchasableRetailPriceFoundException) {
            }
        }

        if (null === $price) {
            throw new NoPurchasableRetailPriceFoundException(__CLASS__);
        }

        return $price;
    }
}
