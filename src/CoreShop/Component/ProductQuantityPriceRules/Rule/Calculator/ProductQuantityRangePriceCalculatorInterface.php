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

namespace CoreShop\Component\ProductQuantityPriceRules\Rule\Calculator;

use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

interface ProductQuantityRangePriceCalculatorInterface
{
    /**
     * @param QuantityRangePriceAwareInterface $subject
     * @param array                            $context
     *
     * @return ProductQuantityPriceRuleInterface[]
     */
    public function getQuantityPriceRulesForProduct(QuantityRangePriceAwareInterface $subject, array $context);

    /**
     * @param QuantityRangeInterface           $range
     * @param QuantityRangePriceAwareInterface $subject
     * @param array                            $context
     *
     * @return int
     */
    public function calculateRangePrice(QuantityRangeInterface $range, QuantityRangePriceAwareInterface $subject, array $context);
}
