<?php
    
namespace CoreShop;

use CoreShop\Base;
use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;

class Cart extends Base {
    
    public static function getAll()
    {
        $list = new Object\CoreShopCart\Listing();
        
        return $list->getObjects();
    }
    
    public static function prepare()
    {
        $cartsFolder = Tool::findOrCreateObjectFolder("/coreshop/carts");
        
        $cart = CoreShopCart::create();
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
    
    public function addItem(Product $product, $amount = 1)
    {
        $items = $this->getItems();
        
        if(!is_array($items))
            $items = array();
        
        $item = new CoreShopCartItem();
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
    
    public function removeItem(CoreShopCartItem $item)
    {
        $item->delete();
    }
    
    public function modifyItem(CoreShopCartItem $item, $amount)
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
            "subtotal" => Tool::formatPrice($this->getSubtotal()),
            "total" => Tool::formatPrice($this->getTotal())
        );
    }
}