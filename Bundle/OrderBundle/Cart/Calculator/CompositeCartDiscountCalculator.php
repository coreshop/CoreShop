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

namespace CoreShop\Bundle\OrderBundle\Cart\Calculator;

use CoreShop\Component\Order\Cart\Calculator\CartDiscountCalculatorInterface;

class CompositeCartDiscountCalculator implements CartDiscountCalculatorInterface
{
    /**
     * @var CartDiscountCalculatorInterface[]
     */
    protected $cartDiscountCalculators;

    /**
     * @param CartDiscountCalculatorInterface[] $cartDiscountCalculators
     */
    public function __construct(array $cartDiscountCalculators)
    {
        $this->cartDiscountCalculators = $cartDiscountCalculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $withTax = true)
    {
        $discount = 0;

        foreach ($this->cartDiscountCalculators as $calculator) {
            $discount += $calculator->getDiscount($subject, $withTax);
        }

        return $discount;
    }
}
