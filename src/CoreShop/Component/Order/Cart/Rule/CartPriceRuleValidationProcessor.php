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

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

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
        return $this->isValid([
            'cart' => $cart,
            'cartPriceRule' => $cartPriceRule,
            'voucher' => $voucherCode
        ], $cartPriceRule);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, RuleInterface $rule)
    {
        /**
         * @var $rule CartPriceRuleInterface
         */
        Assert::isInstanceOf($rule, CartPriceRuleInterface::class);

        if (!$rule->getActive()) {
            return false;
        }

        return $this->ruleConditionsValidationProcessor->isValid(
            $subject,
            $rule->getConditions()
        );
    }
}