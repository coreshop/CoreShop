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

namespace CoreShop\Bundle\RuleBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Prevents saving a rule that references itself, directly or through other rules
 * (e.g. Shipping Rule A uses Shipping Rule B as condition, and B uses A).
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class NoCircularRuleReference extends Constraint
{
    public string $message = 'coreshop.rule.circular_reference';

    public function validatedBy(): string
    {
        return 'coreshop_rule_no_circular_reference';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
