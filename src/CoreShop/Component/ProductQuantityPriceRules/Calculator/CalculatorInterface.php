<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\ProductQuantityPriceRules\Calculator;

use CoreShop\Component\ProductQuantityPriceRules\Exception\NoPriceFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

interface CalculatorInterface
{
    /**
     * @param ProductQuantityPriceRuleInterface $quantityPriceRule
     * @param QuantityRangePriceAwareInterface  $subject
     * @param float                             $quantity
     * @param int                               $originalPrice
     * @param array                             $context
     *
     * @throws NoPriceFoundException
     *
     * @return int
     */
    public function calculateForQuantity(
        ProductQuantityPriceRuleInterface $quantityPriceRule,
        QuantityRangePriceAwareInterface $subject,
        float $quantity,
        int $originalPrice,
        array $context
    );

    /**
     * @param QuantityRangeInterface           $range
     * @param QuantityRangePriceAwareInterface $subject
     * @param int                              $originalPrice
     * @param array                            $context
     *
     * @throws NoPriceFoundException
     *
     * @return int
     */
    public function calculateForRange(
        QuantityRangeInterface $range,
        QuantityRangePriceAwareInterface $subject,
        int $originalPrice,
        array $context
    );
}
