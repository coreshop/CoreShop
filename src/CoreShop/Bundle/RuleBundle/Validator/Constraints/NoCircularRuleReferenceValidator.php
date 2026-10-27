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

use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Rule\Recursion\ResourceIdentifier;
use CoreShop\Component\Rule\Recursion\RuleCycleDetectorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class NoCircularRuleReferenceValidator extends ConstraintValidator
{
    public function __construct(
        private RuleCycleDetectorInterface $cycleDetector,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoCircularRuleReference) {
            throw new UnexpectedTypeException($constraint, NoCircularRuleReference::class);
        }

        if (!$value instanceof RuleInterface) {
            return;
        }

        $cycle = $this->cycleDetector->findCycle($value);

        if (null === $cycle) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('%path%', ResourceIdentifier::describeRulePath($cycle))
            ->addViolation()
        ;
    }
}
