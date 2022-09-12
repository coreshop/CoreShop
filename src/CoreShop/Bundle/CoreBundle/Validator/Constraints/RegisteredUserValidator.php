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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class RegisteredUserValidator extends ConstraintValidator
{
    public function __construct(private CustomerRepositoryInterface $customerRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        /**
         * @var CustomerInterface $value
         */
        Assert::isInstanceOf($value, CustomerInterface::class);

        /**
         * @var RegisteredUser $constraint
         */
        Assert::isInstanceOf($constraint, RegisteredUser::class);

        /** @var CustomerInterface|null $existingCustomer */
        $existingCustomer = $this->customerRepository->findCustomerByEmail($value->getEmail());

        if (null !== $existingCustomer && null !== $existingCustomer->getUser()) {
            $this->context->buildViolation($constraint->message)->atPath('email')->addViolation();
        }
    }
}
