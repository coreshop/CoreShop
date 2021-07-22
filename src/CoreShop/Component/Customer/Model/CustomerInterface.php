<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
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
     * @param string $firstname
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param string $lastname
     */
    public function setLastname($lastname);

    /**
     *  @return CompanyInterface
     */
    public function getCompany();

    /**
     * @param CompanyInterface $company
     */
    public function setCompany($company);

    /**
     * @return string
     */
    public function getGender();

    /**
     * @param string $gender
     */
    public function setGender($gender);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param string $password
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getPasswordResetHash();

    /**
     * @param string $passwordResetHash
     */
    public function setPasswordResetHash($passwordResetHash);

    /**
     * @return bool
     */
    public function getIsGuest();

    /**
     * @param bool $guest
     */
    public function setIsGuest($guest);

    /**
     * @return CustomerGroupInterface[]
     */
    public function getCustomerGroups();

    /**
     * @param CustomerGroupInterface[] $customerGroups
     */
    public function setCustomerGroups($customerGroups);
}
