<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Core\Repository\CustomerRepositoryInterface;
use CoreShop\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class UniqueCustomerValidator extends ConstraintValidator
{
    private CustomerRepositoryInterface $customerRepository;
    private UserRepositoryInterface $userRepository;
    private string $loginIdentifier;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        UserRepositoryInterface $userRepository,
        string $loginIdentifier
    ) {
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->loginIdentifier = $loginIdentifier;
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

        $user = $value->getObjectVar('user');

        if (!$user->getLoginIdentifier()) {
            return;
        }

        if ($user instanceof UserInterface) {
            $otherUser = $this->userRepository->findByLoginIdentifier($user->getLoginIdentifier());

            if ($otherUser) {
                $path = 'email';
                $message = $constraint->messageEmail;

                if ($this->loginIdentifier === 'username') {
                    $path = 'user.loginIdentifier';
                    $message = $constraint->messageUsername;
                }

                $this->context->buildViolation($message)->atPath($path)->addViolation();
                return;
            }
        }

        if (!$value->getEmail()) {
            return;
        }

        /** @var CustomerInterface|null $existingCustomer */
        $existingCustomer = $this->customerRepository->findCustomerByEmail($value->getEmail());

        if (null !== $existingCustomer && null !== $existingCustomer->getUser()) {
            $this->context->buildViolation($constraint->messageEmail)->atPath('email')->addViolation();
        }
    }
}
