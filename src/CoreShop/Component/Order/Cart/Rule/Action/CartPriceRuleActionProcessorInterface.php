<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Cart\Rule\Action;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;

interface CartPriceRuleActionProcessorInterface
{
    /**
     * Apply Rule to Cart.
     */
    public function applyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool;

    /**
     * Remove Rule from Cart.
     */
    public function unApplyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool;
}
