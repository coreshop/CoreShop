<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

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

        $allowedAttributeGroups = $value->getVariantParent()->getAllowedAttributeGroups();
        $attributes = $value->getAttributes();

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
