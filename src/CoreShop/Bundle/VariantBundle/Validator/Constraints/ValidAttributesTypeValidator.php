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

namespace CoreShop\Bundle\VariantBundle\Validator\Constraints;

use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class ValidAttributesTypeValidator extends ConstraintValidator
{
    public function __construct(
        ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        /**
         * @var AttributeInterface $value
         */
        Assert::isInstanceOf($value, AttributeInterface::class);

        /**
         * @var ValidAttributesType $constraint
         */
        Assert::isInstanceOf($constraint, ValidAttributesType::class);

        /**
         * @var AttributeGroupInterface|null $parent
         */
        $parent = $value->getAttributeGroup();

        if (null === $parent) {
            return;
        }

        $concreteListing = new DataObject\Listing();
        $concreteListing->setCondition('path LIKE \'' . $parent->getFullPath() . '/%\'');

        foreach ($concreteListing as $child) {
            if (!$child instanceof AttributeInterface) {
                continue;
            }

            if (!$value instanceof $child) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation()
                ;

                break;
            }
        }
    }
}
