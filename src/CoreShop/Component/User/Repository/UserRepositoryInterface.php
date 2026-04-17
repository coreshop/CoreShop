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

namespace CoreShop\Component\User\Repository;

use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\User\Model\UserInterface;

interface UserRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * @deprecated Use findByResetTokenSecure() instead for proper token validation with hashing
     */
    public function findByResetToken(string $resetToken): ?UserInterface;

    /**
     * Find a user by validating the reset token against the stored hash.
     *
     * @param string $resetToken The raw token provided by the user
     * @param int $ttlSeconds The maximum age of the token in seconds (default: 3600 = 1 hour)
     *
     * @return UserInterface|null The user if token is valid and not expired, null otherwise
     */
    public function findByResetTokenSecure(string $resetToken, int $ttlSeconds = 3600): ?UserInterface;

    public function findByLoginIdentifier(string $value): ?UserInterface;
}
