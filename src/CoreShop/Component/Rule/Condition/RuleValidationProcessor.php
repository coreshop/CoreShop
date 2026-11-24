<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Recursion\RuleRecursionGuard;
use CoreShop\Component\Rule\Recursion\RuleRecursionGuardInterface;

class RuleValidationProcessor implements RuleValidationProcessorInterface
{
    public function __construct(
        private RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor,
        private RuleRecursionGuardInterface $recursionGuard = new RuleRecursionGuard(),
    ) {
    }

    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $params = []): bool
    {
        // A rule that ends up validating itself again (e.g. through a "shippingRule" condition) is invalid
        return $this->recursionGuard->guard(
            RuleRecursionGuardInterface::SCOPE_CONDITIONS,
            $rule,
            $subject,
            fn (): bool => $this->ruleConditionsValidationProcessor->isValid($subject, $rule, $rule->getConditions(), $params),
            false,
        );
    }
}
