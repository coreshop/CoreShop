<?php

namespace CoreShop;

use CoreShop\Base;

class OrderItem extends Base {
    
    public function getTotal()
    {
        return $this->getAmount() * $this->getProductPrice();
    }


    public function getOrder()
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof \Pimcore\Model\Object\CoreShopOrder) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return;
    }
}