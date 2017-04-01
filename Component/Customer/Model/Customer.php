<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Core\Exception\ObjectUnsupportedException;
use CoreShop\Component\Resource\Model\AbstractObject;
use Pimcore\Model\Object;

class Customer extends AbstractObject implements CustomerInterface
{
    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getFirstname()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $firstname
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setFirstname($firstname)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getLastname()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $lastname
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setLastname($lastname)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getGender()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $gender
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setGender($gender)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return CustomerGroupInterface[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getCustomerGroups()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param CustomerGroupInterface[] $customerGroups
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setCustomerGroups($customerGroups)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getEmail()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $email
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setEmail($email)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getPassword()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $password
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setPassword($password)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getAddresses()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $addresses
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setAddresses($addresses)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getNewsletterActive()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $newsletterActive
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setNewsletterActive($newsletterActive)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getNewsletterConfirmed()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $newsletterConfirmed
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setNewsletterConfirmed($newsletterConfirmed)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getIsGuest()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $isGuest
     *
     * @throws ObjectUnsupportedException
     * @return static
     */
    public function setIsGuest($isGuest)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
