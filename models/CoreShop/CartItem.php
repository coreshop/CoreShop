<?php
    
class CoreShop_CartItem extends CoreShop_Base {
    
    public function toArray()
    {
        return array(
            "product" => $this->getProduct()->toArray(),
            "amount" => $this->getAmount()
        );
    }
}