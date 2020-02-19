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
    public function findByResetToken($resetToken);

    /**
     * Find customer by newsletter token.
     *
     * @param string $newsletterToken
     *
     * @return CustomerInterface|null
     */
    public function findByNewsletterToken($newsletterToken);

    /**
     * Find Customer by Identifier.
     *
     * @param string $identifier
     * @param string $value
     * @param bool   $isGuest
     *
     * @return mixed
     */
    public function findUniqueByLoginIdentifier(string $identifier, $value, $isGuest);

    /**
     * Find Customer by Email.
     *
     * @param string $email
     * @param bool   $isGuest
     *
     * @return CustomerInterface|null
     */
    public function findUniqueByEmail($email, $isGuest);

    /**
     * Find Customer by Username.
     *
     * @param string $username
     * @param bool   $isGuest
     *
     * @return CustomerInterface|null
     */
    public function findUniqueByUsername($username, $isGuest);

    /**
     * Find Guest Customer by Email.
     *
     * @param string $email
     *
     * @return CustomerInterface|null
     */
    public function findGuestByEmail($email);

    /**
     * Find Customer by Email.
     *
     * @param string $email
     *
     * @return CustomerInterface|null
     */
    public function findCustomerByEmail($email);

    /**
     * Find Customer by Username.
     *
     * @param string $username
     *
     * @return CustomerInterface|null
     */
    public function findCustomerByUsername($username);
}
