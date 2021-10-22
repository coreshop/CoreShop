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

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class CartPriceRuleValidationProcessor implements CartPriceRuleValidationProcessorInterface
{
    public function __construct(private RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor)
    {
    }

    public function isValidCartRule(OrderInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null): bool
    {
        if (null === $voucherCode && $cartPriceRule->getIsVoucherRule()) {
            return false;
        }

        return $this->isValid($cart, $cartPriceRule, [
            'cartPriceRule' => $cartPriceRule,
            'voucher' => $voucherCode,
        ]);
    }

    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $params = []): bool
    {
        return $this->ruleConditionsValidationProcessor->isValid(
            $subject,
            $rule,
            $rule->getConditions(),
            $params
        );
    }
}
