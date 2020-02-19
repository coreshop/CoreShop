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

namespace CoreShop\Bundle\CoreBundle\Security;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ObjectUserProvider implements UserProviderInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $loginIdentifier;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param string                      $className
     * @param string                      $loginIdentifier
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        $className,
        $loginIdentifier
    )
    {
        $this->customerRepository = $customerRepository;
        $this->className = $className;
        $this->loginIdentifier = $loginIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($userNameOrEmailAddress)
    {
        $customer = $this->customerRepository->findUniqueByLoginIdentifier($this->loginIdentifier, $userNameOrEmailAddress, false);

        if ($customer instanceof CustomerInterface) {
            return $customer;
        }

        throw new UsernameNotFoundException(sprintf('User with email address or username "%s" was not found', $userNameOrEmailAddress));
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof CustomerInterface) {
            throw new UnsupportedUserException();
        }

        /**
         * @var CustomerInterface $refreshedUser
         */
        $refreshedUser = $this->customerRepository->find($user->getId());

        return $refreshedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->className;
    }
}
