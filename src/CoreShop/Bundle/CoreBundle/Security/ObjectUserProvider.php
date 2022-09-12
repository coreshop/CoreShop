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

namespace CoreShop\Bundle\CoreBundle\Security;

use CoreShop\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ObjectUserProvider implements UserProviderInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected string $className,
    ) {
    }

    public function loadUserByUsername(string $username): ?UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $user = $this->userRepository->findByLoginIdentifier($identifier);

        if ($user instanceof UserInterface) {
            return $user;
        }

        throw new UserNotFoundException(sprintf('User with email address or username "%s" was not found', $identifier));
    }

    public function refreshUser(UserInterface $user): ?UserInterface
    {
        if (!$user instanceof \CoreShop\Component\Core\Model\UserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->userRepository->find($user->getId());
    }

    public function supportsClass($class): bool
    {
        return $class === $this->className;
    }
}
