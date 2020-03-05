<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplierInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;

class DiscountPercentActionProcessor implements CartPriceRuleActionProcessorInterface
{
    protected $cartRuleApplier;

    public function __construct(CartRuleApplierInterface $cartRuleApplier)
    {
        $this->cartRuleApplier = $cartRuleApplier;
    }

    /**
     * {@inheritdoc}
     */
    public function applyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool
    {
        $discount = $this->getDiscount($cart, $configuration);

        if ($discount <= 0) {
            return false;
        }

        $this->cartRuleApplier->applyDiscount($cart, $cartPriceRuleItem, $discount, false);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(OrderInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDiscount(OrderInterface $cart, array $configuration): int
    {
        $applyOn = isset($configuration['applyOn']) ? $configuration['applyOn'] : 'total';

        if ('total' === $applyOn) {
            $total = $cart->getTotal(false);
        } else {
            $total = $cart->getSubtotal(false);
        }

        $amount = (int) round(($configuration['percent'] / 100) * $total);

        return $this->getApplicableAmount($amount, $amount);
    }

    protected function getApplicableAmount(int $cartAmount, int $ruleAmount): int
    {
        return min($cartAmount, $ruleAmount);
    }
}
