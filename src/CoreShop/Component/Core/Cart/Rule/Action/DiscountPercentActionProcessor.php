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

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplierInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;

class DiscountPercentActionProcessor implements CartPriceRuleActionProcessorInterface
{
    public function __construct(protected CartRuleApplierInterface $cartRuleApplier)
    {
    }

    public function applyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool
    {
        /**
         * @var StoreInterface $store
         */
        $store = $cart->getStore();

        $discount = $this->getDiscount($cart, $configuration, $store->getUseGrossPrice());

        if ($discount <= 0) {
            return false;
        }

        $this->cartRuleApplier->applyDiscount($cart, $cartPriceRuleItem, $discount, $store->getUseGrossPrice());

        return true;
    }

    public function unApplyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool
    {
        return true;
    }

    protected function getDiscount(OrderInterface $cart, array $configuration, $withTax = false): int
    {
        $total = $cart->getSubtotal($withTax);

        $amount = (int)round(($configuration['percent'] / 100) * $total);

        return $this->getApplicableAmount($amount, $amount);
    }

    protected function getApplicableAmount(int $cartAmount, int $ruleAmount): int
    {
        return min($cartAmount, $ruleAmount);
    }
}
