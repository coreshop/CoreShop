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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception\UnsupportedException;
use Pimcore\Model\Object;

/**
 * Class User
 * @package CoreShop\Model
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

        $conditions = array('email = ?');
        $conditionsValues = array($email);
        $conditionsValues[] = $isGuest ? 1 : 0;

        if (!$isGuest) {
            $conditions[] = '(isGuest = ? OR isGuest IS NULL)';
        } else {
            $conditions[] = 'isGuest = ?';
        }

        $list->setCondition(implode(' AND ', $conditions), $conditionsValues);

        $users = $list->getObjects();

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
     * Auth User.
     *
     * @param $password
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function authenticate($password)
    {
        if ($this->getPassword() == hash('md5', $password)) {
            return true;
        } else {
            throw new \Exception("User and Password doesn't match", 0);
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
        $list->setCondition('customer__id = ?', array($this->getId()));
        $list->setOrderKey('orderDate');
        $list->setOrder('DESC');

        return $list->getObjects();
    }

    /**
     * Get Users latest Cart.
     *
     * @return bool
     */
    public function getLatestCart()
    {
        $list = Cart::getList();
        $list->setCondition('user__id = ?', array($this->getId()));
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');

        $carts = $list->getObjects();

        if (count($carts) > 0) {
            return $carts[0];
        }

        return false;
    }

    /**
     * Check if user is in group.
     *
     * @param CustomerGroup $group
     *
     * @return bool
     */
    public function isInGroup(CustomerGroup $group)
    {
        foreach ($this->getGroups() as $myGroup) {
            if ($myGroup === $group->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * returns email
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return string
     */
    public function getEmail()
    {
        throw new UnsupportedException('getEmail is not supported for '.get_class($this));
    }

    /**
     * returns firstname
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return string
     */
    public function getFirstname()
    {
        throw new UnsupportedException('getFirstname is not supported for '.get_class($this));
    }

    /**
     * returns lastname
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return string
     */
    public function getLastname()
    {
        throw new UnsupportedException('getLastname is not supported for '.get_class($this));
    }

    /**
     * returns password
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return string
     */
    public function getPassword()
    {
        throw new UnsupportedException('getPassword is not supported for '.get_class($this));
    }
}
