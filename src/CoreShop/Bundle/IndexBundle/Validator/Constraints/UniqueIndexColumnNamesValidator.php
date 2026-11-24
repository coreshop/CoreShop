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

namespace CoreShop\Bundle\IndexBundle\Validator\Constraints;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueIndexColumnNamesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueIndexColumnNames) {
            throw new UnexpectedTypeException($constraint, UniqueIndexColumnNames::class);
        }

        if (!is_iterable($value)) {
            return;
        }

        $seen = [];
        $reported = [];

        foreach ($value as $column) {
            if (!$column instanceof IndexColumnInterface) {
                continue;
            }

            $name = (string) $column->getName();

            if ($name === '') {
                continue;
            }

            // MySQL column names are case-insensitive
            $normalized = mb_strtolower($name);

            if (!isset($seen[$normalized])) {
                $seen[$normalized] = true;

                continue;
            }

            if (isset($reported[$normalized])) {
                continue;
            }

            $reported[$normalized] = true;

            $this->context->buildViolation($constraint->message)
                ->setParameter('%name%', $name)
                ->addViolation()
            ;
        }
    }
}
