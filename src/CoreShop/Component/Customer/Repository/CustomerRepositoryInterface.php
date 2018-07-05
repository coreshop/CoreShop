<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
     * @param $resetToken
     *
     * @return CustomerInterface|null
     */
    public function findByResetToken($resetToken);

    /**
     * Find customer by newsletter token.
     *
     * @param $newsletterToken
     *
     * @return CustomerInterface|null
     */
    public function findByNewsletterToken($newsletterToken);

    /**
     * Find Customer by email.
     *
     * @param $email
     * @param $isGuest
     *
     * @return mixed
     */
    public function findUniqueByEmail($email, $isGuest);

    /**
     * Find Guest Customer by Email.
     *
     * @param $email
     *
     * @return mixed
     */
    public function findGuestByEmail($email);

    /**
     * Find Customer by Email.
     *
     * @param $email
     *
     * @return mixed
     */
    public function findCustomerByEmail($email);
}
