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

namespace CoreShop\Component\TierPricing\Rule\Calculator;

use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;
use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use CoreShop\Component\TierPricing\Model\TierPriceAwareInterface;

interface ProductTierPriceCalculatorInterface
{
    /**
     * @param TierPriceAwareInterface $subject
     * @param array                   $context
     *
     * @return ProductSpecificTierPriceRuleInterface[]
     */
    public function getTierPriceRulesForProduct(TierPriceAwareInterface $subject, array $context);

    /**
     * @param ProductTierPriceRangeInterface $range
     * @param TierPriceAwareInterface        $subject
     * @param array                          $context
     *
     * @return int
     */
    public function calculateRangePrice(ProductTierPriceRangeInterface $range, TierPriceAwareInterface $subject, array $context);
}
