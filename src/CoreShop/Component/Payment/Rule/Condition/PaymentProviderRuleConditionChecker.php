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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Payment\Rule\Condition;

use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderRuleInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class PaymentProviderRuleConditionChecker extends AbstractConditionChecker
{
    public function __construct(
        protected RuleValidationProcessorInterface $ruleValidationProcessor,
        protected RepositoryInterface $paymentProviderRuleRepository,
    ) {
    }

    public function isPaymentProviderRuleValid(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $configuration,
    ): bool {
        $paymentProviderRuleId = $configuration['paymentProviderRule'];
        $paymentProviderRule = $this->paymentProviderRuleRepository->find($paymentProviderRuleId);

        if ($paymentProviderRule instanceof PaymentProviderRuleInterface) {
            return $this->ruleValidationProcessor->isValid($paymentProvider, $paymentProviderRule, ['payable' => $payable]);
        }

        return false;
    }
}
