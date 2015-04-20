<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\User;

use CoreShop\Base;
use CoreShop\Tool;

use Pimcore\Model\Object;
use Pimcore\Model\Object\CoreShopCart;

class UserAbstract extends Base implements \CoreShop\Plugin\User
{
    public static function getUniqueByEmail($email)
    {
        $list = self::getByEmail($email);

        $users = $list->getObjects();

        if (count($users) > 0) {
            return $users[0];
        }

        return false;
    }

    public function authenticate($password)
    {
        if ($this->getPassword() == hash("md5", $password)) {
            return true;
        }
        else {
            throw new \Exception("User and Password doesn't match", 0);
        }

        return false;
    }

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

    public function getOrders()
    {
        $list = new Object\CoreShopOrder\Listing();
        $list->setCondition("customer__id = ?", array($this->getId()));

        return $list->getObjects();
    }

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
}