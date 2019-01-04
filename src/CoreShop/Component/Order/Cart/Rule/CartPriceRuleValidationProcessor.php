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

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class CartPriceRuleValidationProcessor implements CartPriceRuleValidationProcessorInterface
{
    /**
     * @var RuleConditionsValidationProcessorInterface
     */
    private $ruleConditionsValidationProcessor;

    /**
     * @param RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor
     */
    public function __construct(RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor)
    {
        $this->ruleConditionsValidationProcessor = $ruleConditionsValidationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidCartRule(CartInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null)
    {
        if ($cartPriceRule->getIsVoucherRule() && null === $voucherCode) {
            return false;
        }

        return $this->isValid($cart, $cartPriceRule, [
            'cartPriceRule' => $cartPriceRule,
            'voucher' => $voucherCode,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, $params = [])
    {
        return $this->ruleConditionsValidationProcessor->isValid(
            $subject,
            $rule,
            $rule->getConditions(),
            $params
        );
    }
}
