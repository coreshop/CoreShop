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

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

class NestedConditionChecker implements ConditionCheckerInterface
{
    public function __construct(
        protected RuleConditionsValidationProcessorInterface $ruleConditionsValidationProcessor,
    ) {
    }

    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, array $params = []): bool
    {
        $operator = $configuration['operator'];
        $valid = $operator === 'and';

        foreach ($configuration['conditions'] as $condition) {
            $conditionValid = $this->ruleConditionsValidationProcessor->isValid($subject, $rule, [$condition], $params);

            switch ($operator) {
                case 'and':
                    if (!$conditionValid) {
                        $valid = false;

                        break 2;
                    }

                    break;
                case 'or':
                    if ($conditionValid) {
                        $valid = true;

                        break 2;
                    }

                    break;
            }
        }

        if ('not' === $operator) {
            return !$valid;
        }

        return $valid;
    }
}
