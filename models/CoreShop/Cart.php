<?php
    
class CoreShop_Cart extends CoreShop_Base {
    
    public static function getAll()
    {
        $list = new Object_CoreShopCart_List();
        
        return $list->getObjects();
    }
    
    public static function create()
    {
        $cartsFolder = Object_Folder::getByPath("/coreshop/carts");
        
        $cart = new Object_CoreShopCart();
        $cart->setKey(uniqid());
        $cart->setParent($cartsFolder);
        $cart->setPublished(true);
        $cart->save();
        
        return $cart;
    }
    
    public function addItem(CoreShop_Product $product, $amount = 1)
    {
        $items = $this->getItems();
        
        if(!is_array($items))
            $items = array();
        
        $item = new Object_CoreShopCartItem();
        $item->setKey(uniqid());
        $item->setParent($this);
        $item->setAmount($amount);
        $item->setProduct($product);
        $item->setPublished(true);
        $item->save();
        
        $items[] = $item;

        $this->setItems($items);
        $this->save(true);
        
        return $item;
    }
    
    public function removeItem(CoreShop_CartItem $item)
    {
        $item->delete();
    }
    
    public function toArray()
    {
        $items = array();
        $total = 0;
        
        foreach($this->getItems() as $item)
        {
            $items[] = $item->toArray();
            
            $total += $item->getAmount() * $item->getProduct()->getPrice();
        }
        
        return array(
            "user" => $this->user ? $this->user->toArray() : null,
            "items" => $items,
            "total" => $total,
            "totalFormatted" => CoreShop_Tool::formatPrice($total)
        );
    }
}