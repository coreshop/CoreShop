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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Locale\Model\LocaleAwareInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CustomerInterface extends ResourceInterface, PimcoreModelInterface, UserInterface, EquatableInterface, LocaleAwareInterface
{
    const CORESHOP_ROLE_DEFAULT = 'ROLE_USER';
    const CORESHOP_ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @return string
     */
    public function getSalutation();

    /**
     * @param string $salutation
     */
    public function setSalutation($salutation);

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @param $firstname
     *
     * @return static
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param $lastname
     *
     * @return static
     */
    public function setLastname($lastname);

    /**
     * @return mixed
     */
    public function getGender();

    /**
     * @param $gender
     *
     * @return static
     */
    public function setGender($gender);

    /**
     * @return mixed
     */
    public function getEmail();

    /**
     * @param $email
     *
     * @return static
     */
    public function setEmail($email);

    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @param $password
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getPasswordResetHash();

    /**
     * @param $passwordResetHash
     */
    public function setPasswordResetHash($passwordResetHash);

    /**
     * @return bool
     */
    public function getIsGuest();

    /**
     * @param bool $guest
     *
     * @return static
     */
    public function setIsGuest($guest);

    /**
     * @return CustomerGroupInterface[]
     */
    public function getCustomerGroups();

    /**
     * @param CustomerGroupInterface[] $customerGroups
     *
     * @return static
     */
    public function setCustomerGroups($customerGroups);
}
