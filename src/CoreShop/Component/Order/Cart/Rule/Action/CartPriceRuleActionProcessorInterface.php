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

namespace CoreShop\Component\Order\Cart\Rule\Action;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;

interface CartPriceRuleActionProcessorInterface
{
    /**
     * Apply Rule to Cart.
     *
     * @param CartInterface $cart
     * @param array $configuration
     * @param ProposalCartPriceRuleItemInterface $cartPriceRuleItem
     *
     * @return bool
     */
    public function applyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem);

    /**
     * Remove Rule from Cart.
     *
     * @param CartInterface $cart
     * @param array $configuration
     * @param ProposalCartPriceRuleItemInterface $cartPriceRuleItem
     *
     * @return bool
     */
    public function unApplyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem);
}
