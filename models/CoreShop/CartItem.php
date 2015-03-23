<?php
    
class CoreShop_CartItem extends CoreShop_Base {
    
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
            "price" => CoreShop_Tool::formatPrice($this->product->getPrice()),
            "total" => CoreShop_Tool::formatPrice($this->getTotal()),
        );
    }
}