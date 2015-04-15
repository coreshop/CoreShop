<?php
    
namespace CoreShop;

use CoreShop\Base;
use CoreShop\Plugin;
use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;
use Pimcore\Model\Object\CoreShopCartRule;

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

    public function getDiscount()
    {
        $cartRule = $this->getCartRule();

        if($cartRule instanceof CoreShopCartRule)
            return $cartRule->getDiscount();

        return 0;
    }
    
    public function getSubtotal()
    {
        $subtotal = 0;
        
        foreach($this->getItems() as $item)
        {
            $subtotal += ($item->getAmount() * $item->getProduct()->getProductPrice());
        }
        
        return $subtotal;
    }

    public function getShipping()
    {
        $session = Tool::getSession();

        //check for existing shipping
        if(array_key_exists("shippingProvider", $session->order) && $session->order['deliveryProvider'] instanceof \CoreShop\Plugin\Shipping) {
            return $session->order['shippingProvider']->getShipping($this);
        }

        //get all provider and choose cheapest
        $providers = Plugin::getShippingProviders($this);
        $cheapestProvider = null;

        foreach($providers as $p)
        {
            if($cheapestProvider === null)
                $cheapestProvider = $p;
            else if($cheapestProvider->getShipping($this) > $p->getShipping($this))
                $cheapestProvider = $p;
        }

        if($cheapestProvider instanceof \CoreShop\Plugin\Shipping)
            return $cheapestProvider->getShipping($this);

        return 0;
    }
    
    public function getTotal()
    {
        $subtotal = $this->getSubtotal();
        $discount = $this->getDiscount();
        $shipping = $this->getShipping();

        return ($subtotal  + $shipping) - $discount;
    }
    
    public function addItem(Product $product, $amount = 1)
    {
        $items = $this->getItems();
        
        if(!is_array($items))
            $items = array();

        foreach($items as $item)
        {
            if($item->getProduct()->getId() == $product->getId())
            {
                $item->setAmount($item->getAmount()+1);
                $item->save();

                return $item;
            }
        }
        
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

    public function removeCartRule()
    {
        if($this->getCartRule() instanceof CoreShopCartRule)
        {
            $this->setCartRule(null);
            $this->save();
        }

        return true;
    }

    public function addCartRule(CoreShopCartRule $cartRule)
    {
        $this->removeCartRule();

        $this->setCartRule($cartRule);
        $this->save();
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