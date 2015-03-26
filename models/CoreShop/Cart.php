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
    
    public function hasPhysicalItems()
    {
        foreach($this->getItems() as $item)
        {
            if($item->getProduct()->getIsDownloadProduct() !== "yes")
            {
                return true;
            }
        }
        
        return false;
    }
    
    public function getSubtotal()
    {
        $subtotal = 0;
        
        foreach($this->getItems() as $item)
        {
            $subtotal += ($item->getAmount() * $item->getProduct()->getPrice());
        }
        
        return $subtotal;
    }
    
    public function getTotal()
    {
        $subtotal = $this->getSubtotal();;
        
        return $subtotal;
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
    
    public function modifyItem(CoreShop_CartItem $item, $amount)
    {
        $item->setAmount($amount);
        $item->save();
    }
    
    public function toArray()
    {
        $items = array();
        
        foreach($this->getItems() as $item)
        {
            $items[] = $item->toArray();
        }
        
        return array(
            "user" => $this->user ? $this->user->toArray() : null,
            "items" => $items,
            "subtotal" => CoreShop_Tool::formatPrice($this->getSubtotal()),
            "total" => CoreShop_Tool::formatPrice($this->getTotal())
        );
    }
}