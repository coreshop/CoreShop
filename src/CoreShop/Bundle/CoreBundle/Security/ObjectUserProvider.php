<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Security;

use CoreShop\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ObjectUserProvider implements UserProviderInterface
{
    protected UserRepositoryInterface $userRepository;
    protected string $className;

    public function __construct(
        UserRepositoryInterface $userRepository,
        string $className
    )
    {
        $this->userRepository = $userRepository;
        $this->className = $className;
    }

    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier)
    {
        $user = $this->userRepository->findByLoginIdentifier($identifier);

        if ($user instanceof \CoreShop\Component\Core\Model\UserInterface) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('User with email address or username "%s" was not found', $identifier));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof \CoreShop\Component\Core\Model\UserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->userRepository->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $class === $this->className;
    }
}
