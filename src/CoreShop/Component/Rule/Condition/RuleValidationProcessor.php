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

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class RuleValidationProcessor implements RuleValidationProcessorInterface
{
    public function __construct(
        private RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor,
    ) {
    }

    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $params = []): bool
    {
        return $this->ruleConditionsValidationProcessor->isValid($subject, $rule, $rule->getConditions(), $params);
    }
}
