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

namespace CoreShop\Bundle\VariantBundle\Validator\Constraints;

use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ValidAttributesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /**
         * @var ProductVariantAwareInterface $value
         */
        Assert::isInstanceOf($value, ProductVariantAwareInterface::class);

        /**
         * @var ValidAttributes $constraint
         */
        Assert::isInstanceOf($constraint, ValidAttributes::class);

        $allowedAttributeGroups = $value->getVariantParent()->getAllowedAttributeGroups() ?? [];
        $attributes = $value->getAttributes() ?? [];

        if (count($allowedAttributeGroups) !== count($attributes)) {
            $this->context->buildViolation($constraint->message)->addViolation();

            return;
        }

        $foundGroups = [];
        foreach ($attributes as $attribute) {
            $group = $attribute->getAttributeGroup();

            if (!$group) {
                continue;
            }

            if (!in_array($group, $allowedAttributeGroups, true) || in_array($group->getId(), $foundGroups, true)) {
                $this->context->buildViolation($constraint->message)->addViolation();

                return;
            }

            $foundGroups[] = $group->getId();
        }
    }
}
