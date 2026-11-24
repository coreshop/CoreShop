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

namespace CoreShop\Behat\Context\Domain;

use CoreShop\Bundle\RuleBundle\Validator\Constraints\NoCircularRuleReference;
use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait CircularRuleReferenceTrait
{
    abstract protected function getValidator(): ValidatorInterface;

    /**
     * Validates $rule the same way the admin does before saving it.
     */
    private function hasCircularRuleReferenceViolation(RuleInterface $rule): bool
    {
        foreach ($this->getValidator()->validate($rule, null, ['coreshop']) as $violation) {
            if ($violation->getConstraint() instanceof NoCircularRuleReference) {
                return true;
            }
        }

        return false;
    }
}
