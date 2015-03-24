<?php
    
class CoreShop_Payment extends CoreShop_Base {
    
    public static function findByTransactionIdentifier($transactionIdentification) {
        $list = Object_CoreShopPayment::getByTransactionIdentifier($transactionIdentification);

        $users = $list->getObjects();

        if (count($users) > 0) {
            return $users[0];
        }

        return false;
    }

    public function getOrder() {
        $parent = $this->getParent();

        do {
            if ($parent instanceof Object_CoreShopOrder) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return false;
    }
}