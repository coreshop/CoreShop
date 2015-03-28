<?php

namespace CoreShop;

use CoreShop\Base;

class OrderItem extends Base {
    
    public function getTotal()
    {
        return $this->getAmount() * $this->getPrice();
    }
}