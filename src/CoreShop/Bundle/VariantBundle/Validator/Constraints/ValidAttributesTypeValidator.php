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

use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class ValidAttributesTypeValidator extends ConstraintValidator
{
    public function __construct()
    {
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
         * @var null|AttributeGroupInterface $parent
         */
        $parent = $value->getAttributeGroup();

        if (null === $parent) {
            return;
        }

        foreach ($parent->getChildren() as $child) {
            if (!$value instanceof $child) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                break;
            }
        }
    }
}
