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

class DiscountPercentActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem)
    {
        $discountNet = $this->getDiscount($cart, false, $configuration);
        $discountGross = $this->getDiscount($cart, true, $configuration);

        if ($discountGross <= 0) {
            return false;
        }

        $cartPriceRuleItem->setDiscount($cartPriceRuleItem->getDiscount(false) + $discountNet, false);
        $cartPriceRuleItem->setDiscount($cartPriceRuleItem->getDiscount(true) + $discountGross, true);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDiscount(CartInterface $cart, $withTax, array $configuration)
    {
        $applyOn = isset($configuration['applyOn']) ? $configuration['applyOn'] : 'total';

        if ('total' === $applyOn) {
            $total = $cart->getTotal($withTax);
        } else {
            $total = $cart->getSubtotal($withTax);
        }

        $amount = (int) round(($configuration['percent'] / 100) * $total);
        $cartAmount = $total - $cart->getDiscount($withTax);

        return $this->getApplicableAmount($cartAmount, $amount);
    }

    /**
     * @param $cartAmount
     * @param $ruleAmount
     *
     * @return int
     */
    protected function getApplicableAmount($cartAmount, $ruleAmount)
    {
        return min($cartAmount, $ruleAmount);
    }
}
