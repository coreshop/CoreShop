<?php

namespace CoreShop;

use CoreShop\Base;

use Pimcore\Model\Object;

class Payment extends Base {

    public static function findByTransactionIdentifier($transactionIdentification) {
        $list = Object\CoreShopPayment::getByTransactionIdentifier($transactionIdentification);

        $users = $list->getObjects();

        if (count($users) > 0) {
            return $users[0];
        }

        return false;
    }

    public function getOrder() {
        $parent = $this->getParent();

        do {
            if ($parent instanceof Object\CoreShopOrder) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return false;
    }
}