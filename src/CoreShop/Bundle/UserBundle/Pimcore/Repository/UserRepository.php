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

namespace CoreShop\Bundle\UserBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\User\Model\UserInterface;
use CoreShop\Component\User\Repository\UserRepositoryInterface;

class UserRepository extends PimcoreRepository implements UserRepositoryInterface
{
    /**
     * @deprecated Use findByResetTokenSecure() instead for proper token validation with hashing
     */
    public function findByResetToken(string $resetToken): ?UserInterface
    {
        $list = $this->getList();
        $list->setCondition('passwordResetHash = ?', [$resetToken]);
        $objects = $list->load();

        if (count($objects) === 1 && $objects[0] instanceof UserInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findByResetTokenSecure(string $resetToken, int $ttlSeconds = 3600): ?UserInterface
    {
        // Hash the provided token to compare against stored hash
        $tokenHash = hash('sha256', $resetToken);

        $list = $this->getList();
        $list->setCondition('passwordResetHash = ?', [$tokenHash]);
        $objects = $list->load();

        if (count($objects) !== 1 || !$objects[0] instanceof UserInterface) {
            return null;
        }

        $user = $objects[0];

        // Check token expiration (TTL)
        $createdAt = $user->getPasswordResetHashCreatedAt();
        if ($createdAt === null) {
            return null;
        }

        $now = new \DateTimeImmutable();
        $expiresAt = $createdAt->modify('+' . $ttlSeconds . ' seconds');

        if ($now > $expiresAt) {
            // Token has expired
            return null;
        }

        return $user;
    }

    public function findByLoginIdentifier(string $value): ?UserInterface
    {
        $list = $this->getList();

        $conditions = ['loginIdentifier = ?'];
        $conditionsValues = [$value];

        $list->setCondition(implode(' AND ', $conditions), $conditionsValues);
        $list->load();

        $users = $list->getObjects();

        if (count($users) > 0 && $users[0] instanceof UserInterface) {
            return $users[0];
        }

        return null;
    }
}
