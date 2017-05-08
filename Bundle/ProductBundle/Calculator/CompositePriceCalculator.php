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
 *
*/

namespace CoreShop\Bundle\ProductBundle\Calculator;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;

class CompositePriceCalculator implements ProductPriceCalculatorInterface
{
    /**
     * @var ProductPriceCalculatorInterface[]
     */
    protected $priceRuleCalculators;

    /**
     * @param ProductPriceCalculatorInterface[] $priceRuleCalculators
     */
    public function __construct(array $priceRuleCalculators)
    {
        $this->priceRuleCalculators = $priceRuleCalculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($subject)
    {
        $price = false;

        foreach ($this->priceRuleCalculators as $calculator) {
            $actionPrice = $calculator->getPrice($subject);

            if (false !== $actionPrice && null !== $actionPrice) {
                $price = $actionPrice;
            }
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $price)
    {
        $discount = 0;

        foreach ($this->priceRuleCalculators as $calculator) {
            $discount += $calculator->getDiscount($subject, $price);
        }

        return $discount;
    }
}
