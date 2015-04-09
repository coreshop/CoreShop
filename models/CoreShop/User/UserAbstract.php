<?php

namespace CoreShop\User;

use CoreShop\Base;
use CoreShop\Tool;

use Pimcore\Model\Object;

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
}