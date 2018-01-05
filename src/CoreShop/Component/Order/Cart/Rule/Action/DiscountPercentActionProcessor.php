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

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;

class DiscountPercentActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyRule(CartInterface $cart, array $configuration)
    {
        return $this->getDiscount($cart, false, $configuration) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(CartInterface $cart, array $configuration)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(CartInterface $cart, $withTax, array $configuration)
    {
        $amount = (int) round(($configuration['percent'] / 100) * $cart->getSubtotal($withTax));
        $cartAmount = $cart->getSubtotal($withTax) - $cart->getDiscount($withTax);

        return $this->getApplicableAmount($cartAmount, $amount);
    }

    /**
     * @param $cartAmount
     * @param $ruleAmount
     * @return int
     */
    protected function getApplicableAmount($cartAmount, $ruleAmount)
    {
        return min($cartAmount, $ruleAmount);
    }
}
