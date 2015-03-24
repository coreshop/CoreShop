<?php
    
class CoreShop_OrderItem extends CoreShop_Base {
    
    public function getTotal()
    {
        return $this->getAmount() * $this->getPrice();
    }
}