<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositeDiscountCalculator implements ProductDiscountCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $discountCalculator;

    /**
     * @param PrioritizedServiceRegistryInterface $discountCalculator
     */
    public function __construct($discountCalculator)
    {
        $this->discountCalculator = $discountCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(ProductInterface $subject, array $context, $price)
    {
        $discount = 0;

        /**
         * @var ProductDiscountCalculatorInterface $calculator
         */
        foreach ($this->discountCalculator->all() as $calculator) {
            $discount += $calculator->getDiscount($subject, $context, $price);
        }

        return $discount;
    }
}
