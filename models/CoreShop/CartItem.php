<?php
    
namespace CoreShop;

use CoreShop\Base;
    
class CartItem extends Base {
    
    public function getTotal()
    {
        return $this->getAmount() * $this->product->getPrice();
    }
    
    public function toArray()
    {
        return array(
            "id" => $this->getId(),
            "product" => $this->getProduct()->toArray(),
            "amount" => $this->getAmount(),
            "price" => Tool::formatPrice($this->product->getPrice()),
            "total" => Tool::formatPrice($this->getTotal()),
        );
    }
}