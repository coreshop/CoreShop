<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
         * @var UniqueCustomer $constraint
         */
        Assert::isInstanceOf($constraint, UniqueCustomer::class);

        /** @var CustomerInterface|null $existingCustomer */
        $existingCustomer = $this->customerRepository->findCustomerByEmail($value->getEmail());

        if (null !== $existingCustomer && null !== $existingCustomer->getUser()) {
            $this->context->buildViolation($constraint->messageEmail)->atPath('email')->addViolation();
        }
    }
}
