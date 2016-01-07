<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Tool;

use Pimcore\Model\Object;
use Pimcore\Model\Object\CoreShopCart;

class User extends Base
{
    /**
     * Get User by E-Mail
     *
     * @param $email
     * @return bool
     */
    public static function getUniqueByEmail($email)
    {
        $list = self::getByEmail($email);

        $users = $list->getObjects();

        if (count($users) > 0) {
            return $users[0];
        }

        return false;
    }

    /**
     * Auth User
     *
     * @param $password
     * @return bool
     * @throws \Exception
     */
    public function authenticate($password)
    {
        if ($this->getPassword() == hash("md5", $password)) {
            return true;
        }
        else {
            throw new \Exception("User and Password doesn't match", 0);
        }
    }

    /**
     * Get User address by Name
     *
     * @param $name
     * @return bool
     */
    public function findAddressByName($name)
    {
        foreach($this->getAddresses() as $address)
        {
            if($address->getName() == $name) {
                return $address;
            }
        }

        return false;
    }

    /**
     * Get User Orders
     *
     * @return array
     */
    public function getOrders()
    {
        $list = new Object\CoreShopOrder\Listing();
        $list->setCondition("customer__id = ?", array($this->getId()));

        return $list->getObjects();
    }

    /**
     * Get Users latest Cart
     *
     * @return bool
     */
    public function getLatestCart()
    {
        $list = new CoreShopCart\Listing();
        $list->setCondition("user__id = ?", array($this->getId()));
        $list->setOrderKey("o_creationDate");
        $list->setOrder("DESC");

        $carts = $list->getObjects();

        if(count($carts) > 0)
            return $carts[0];

        return false;
    }

    /**
     * returns email
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return string
     */
    public function getEmail() {
        throw new UnsupportedException("getEmail is not supported for " . get_class($this));
    }

    /**
     * returns firstname
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return string
     */
    public function getFirstname() {
        throw new UnsupportedException("getFirstname is not supported for " . get_class($this));
    }

    /**
     * returns lastname
     * this method has to be overwritten in Pimcore Object
     *
     * @throws UnsupportedException
     * @return string
     */
    public function getLastname() {
        throw new UnsupportedException("getLastname is not supported for " . get_class($this));
    }
}