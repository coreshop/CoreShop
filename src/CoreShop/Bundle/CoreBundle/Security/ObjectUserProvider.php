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
use CoreShop\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ObjectUserProvider implements UserProviderInterface
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var string
     */
    protected $className;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param string                      $className
     */
    public function __construct(UserRepositoryInterface $userRepository, $className)
    {
        $this->userRepository = $userRepository;
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($emailAddress)
    {
        $customer = $this->userRepository->findByEmail($emailAddress);
        if ($customer instanceof \CoreShop\Component\Core\Model\UserInterface) {
            return $customer;
        }

        throw new UsernameNotFoundException(sprintf('User with email address %s was not found', $emailAddress));
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof \CoreShop\Component\Core\Model\UserInterface) {
            throw new UnsupportedUserException();
        }

        /**
         * @var \CoreShop\Component\Core\Model\UserInterface $refreshedUser
         */
        $refreshedUser = $this->userRepository->find($user->getId());

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
