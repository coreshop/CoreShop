<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Calculator;

use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositePurchasableDiscountCalculator implements PurchasableDiscountCalculatorInterface
{
    protected $discountCalculators;

    public function __construct(PrioritizedServiceRegistryInterface $discountCalculators)
    {
        $this->discountCalculators = $discountCalculators;
    }

    /**
     * {@inheritdoc}
     */
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
