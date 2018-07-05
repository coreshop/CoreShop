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

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableDiscountCalculator implements PurchasableDiscountCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $discountCalculators;

    /**
     * @param PrioritizedServiceRegistryInterface $discountCalculators
     */
    public function __construct(PrioritizedServiceRegistryInterface $discountCalculators)
    {
        $this->discountCalculators = $discountCalculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(PurchasableInterface $purchasable, $basePrice)
    {
        $discounts = 0;

        /**
         * @var PurchasableDiscountCalculatorInterface
         */
        foreach ($this->discountCalculators->all() as $calculator) {
            $discounts += $calculator->getDiscount($purchasable, $basePrice);
        }

        return $discounts;
    }
}
