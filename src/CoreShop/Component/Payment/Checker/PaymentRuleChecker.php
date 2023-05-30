<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Payment\Checker;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use Coreshop\Component\Payment\Model\PaymentRuleGroupInterface;
use Coreshop\Component\Payment\Model\PaymentRuleInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class PaymentRuleChecker implements PaymentRuleCheckerInterface
{
    public function __construct(
        protected RuleValidationProcessorInterface $ruleValidationProcessor,
    ) {
    }

    public function findValidPaymentRule(
        PaymentProviderInterface $paymentProvider,
        ResourceInterface $subject = null,
    ): ?PaymentRuleInterface {
        $paymentRules = $paymentProvider->getPaymentRules();

        if (count($paymentRules) === 0) {
            return null;
        }

        foreach ($paymentRules as $rule) {
            $isValid = false;
            if ($subject) {
                $isValid = $this->ruleValidationProcessor->isValid($paymentProvider, $rule instanceof PaymentRuleInterface ? $rule : $rule->getPaymentRule(), [
                    'payable' => $subject,
                ]);
            }

            if ($isValid === false && ($rule instanceof PaymentRuleGroupInterface && $rule->getStopPropagation() === true)) {
                return null;
            }

            if ($isValid === true) {
                return $rule instanceof PaymentRuleInterface ? $rule : $rule->getPaymentRule();
            }
        }

        return null;
    }
}
