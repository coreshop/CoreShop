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

namespace CoreShop\Component\Core\Cart\Rule\Applier;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;

interface DiscountApplierInterface
{
    /**
     * @param CartInterface                      $cart
     * @param ProposalCartPriceRuleItemInterface $cartPriceRuleItem
     * @param int                                $discount
     * @param bool                               $withTax
     */
    public function applyDiscount(
        CartInterface $cart,
        ProposalCartPriceRuleItemInterface $cartPriceRuleItem,
        int $discount,
        $withTax = false
    );
}
