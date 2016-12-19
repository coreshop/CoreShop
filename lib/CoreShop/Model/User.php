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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Customer\Group;
use Pimcore\Model\Object;

/**
 * Class User
 * @package CoreShop\Model
 *
 * @method static Object\Listing\Concrete getByFirstname ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByLastname ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByGender ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByGroups ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByEmail ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPassword ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByAddresses ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByNewsletterActive ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByNewsletterConfirmed ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByIsGuest ($value, $limit = 0)
 */
class User extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopUser';

    /**
     * Get User by E-Mail.
     *
     * @param $email string
     * @param $isGuest boolean
     *
     * @return bool
     */
    public static function getUniqueByEmail($email, $isGuest = false)
    {
        $list = self::getList();

        $conditions = ['email = ?'];
        $conditionsValues = [$email];
        $conditionsValues[] = $isGuest ? 1 : 0;

        if (!$isGuest) {
            $conditions[] = '(isGuest = ? OR isGuest IS NULL)';
        } else {
            $conditions[] = 'isGuest = ?';
        }

        $list->setCondition(implode(' AND ', $conditions), $conditionsValues);

        $users = $list->load();

        if (count($users) > 0) {
            return $users[0];
        }

        return false;
    }

    /**
     * Get Guest by email.
     *
     * @param $email
     *
     * @return User|bool
     */
    public static function getGuestByEmail($email)
    {
        return self::getUniqueByEmail($email, true);
    }

    /**
     * Get User by email.
     *
     * @param $email
     *
     * @return User|bool
     */
    public static function getUserByEmail($email)
    {
        return self::getUniqueByEmail($email, false);
    }

    /**
     * get folder for order
     *
     * @return Object\Folder
     */
    public function getPathForAddresses()
    {
        return Object\Service::createFolderByPath($this->getFullPath() . "/addresses");
    }

    /**
     * Auth User.
     *
     * @param $password
     *
     * @return bool
     *
     * @throws Exception
     */
    public function authenticate($password)
    {
        if ($this->getPassword() == hash('md5', $password)) {
            return true;
        } else {
            throw new Exception("User and Password doesn't match", 0);
        }
    }

    /**
     * Get User address by Name.
     *
     * @param $name
     *
     * @return bool
     */
    public function findAddressByName($name)
    {
        foreach ($this->getAddresses() as $address) {
            if ($address->getName() == $name) {
                return $address;
            }
        }

        return false;
    }

    /**
     * Get User Orders.
     *
     * @return array
     */
    public function getOrders()
    {
        $list = Order::getList();
        $list->setCondition('customer__id = ?', [$this->getId()]);
        $list->setOrderKey('orderDate');
        $list->setOrder('DESC');

        return $list->load();
    }

    /**
     * Get User Carts
     *
     * @return Cart[]
     */
    public function getCarts()
    {
        return \CoreShop::getTools()->getCartManager()->getCarts($this);
    }

    /**
     * Get Users latest Cart.
     *
     * @return bool
     */
    public function getLatestCart()
    {
        $list = Cart::getList();
        $list->setCondition('user__id = ?', [$this->getId()]);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');

        $carts = $list->load();

        if (count($carts) > 0) {
            return $carts[0];
        }

        return false;
    }

    /**
     * Check if user is in group.
     *
     * @param Group $group
     *
     * @return bool
     */
    public function isInGroup(Group $group)
    {
        foreach ($this->getCustomerGroups() as $myGroup) {
            if ($myGroup->getId() === $group->getId()) {
                return true;
            }
        }

        return false;
    }

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
     */
    public function setGender($gender)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Group[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getCustomerGroups()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Group[] $customerGroups
     *
     * @throws ObjectUnsupportedException
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
     */
    public function setIsGuest($isGuest)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
