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

use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\TierPricing\Model\ProductSpecificTierPriceRuleInterface;

interface ProductTierPriceCalculatorInterface
{
    /**
     * @param ProductInterface $subject
     * @param array            $context
     *
     * @return array|ProductSpecificTierPriceRuleInterface[]
     */
    public function getTierPriceRulesForProduct(ProductInterface $subject, array $context);

    /**
     * @param ProductInterface  $subject
     * @param CartItemInterface $cartItem
     * @param array             $context
     *
     * @return bool|int
     */
    public function getTierPriceForCartItem(ProductInterface $subject, CartItemInterface $cartItem, array $context);
}
