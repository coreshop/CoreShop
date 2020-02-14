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

namespace CoreShop\Component\Customer\Repository;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

interface CustomerRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * Find customer by reset token.
     *
     * @param string $resetToken
     *
     * @return CustomerInterface|null
     */
    public function findByResetToken(string $resetToken): ?CustomerInterface;

    /**
     * Find customer by newsletter token.
     *
     * @param string $newsletterToken
     *
     * @return CustomerInterface|null
     */
    public function findByNewsletterToken(string $newsletterToken): ?CustomerInterface;

    /**
     * Find Customer by email.
     *
     * @param string $email
     * @param bool   $isGuest
     *
     * @return CustomerInterface|null
     */
    public function findUniqueByEmail(string $email, bool $isGuest): ?CustomerInterface;

    /**
     * Find Guest Customer by Email.
     *
     * @param string $email
     *
     * @return CustomerInterface|null
     */
    public function findGuestByEmail(string $email): ?CustomerInterface;

    /**
     * Find Customer by Email.
     *
     * @param string $email
     *
     * @return CustomerInterface|null
     */
    public function findCustomerByEmail(string $email): ?CustomerInterface;
}
