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

namespace CoreShop\Bundle\AddressBundle\Validator\Constraints;

use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidAddressIdentifierValidator extends ConstraintValidator
{
    public function __construct(
        private RepositoryInterface $addressIdentifierRepository,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidAddressIdentifier) {
            throw new UnexpectedTypeException($constraint, ValidAddressIdentifier::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        $addressIdentifier = $this->addressIdentifierRepository->find($value);

        if (!$addressIdentifier instanceof AddressIdentifierInterface) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%address_identifier%', $value)
                ->addViolation()
            ;
        }
    }
}
