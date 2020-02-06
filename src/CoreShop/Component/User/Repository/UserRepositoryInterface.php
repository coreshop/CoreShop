<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\User\Repository;

use CoreShop\Component\User\Model\UserInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

interface UserRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * Find user by reset token.
     *
     * @param string $resetToken
     *
     * @return UserInterface|null
     */
    public function findByResetToken($resetToken);

    /**
     * Find User by email.
     *
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function findByEmail($email);
}
