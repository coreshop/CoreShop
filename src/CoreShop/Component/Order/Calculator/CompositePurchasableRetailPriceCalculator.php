<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Exception\NoPurchasableRetailPriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableRetailPriceCalculator implements PurchasableRetailPriceCalculatorInterface
{
    public function __construct(protected PrioritizedServiceRegistryInterface $calculators)
    {
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
