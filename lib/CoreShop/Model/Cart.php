<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model;

use CoreShop\Plugin;
use CoreShop\Tool;

use Pimcore\Model\Object\CoreShopProduct;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;
use Pimcore\Model\Object\CoreShopCartRule;

class Cart extends Base {

    public static function getAll()
    {
        $list = new CoreShopCart\Listing();
        
        return $list->getObjects();
    }
    
    public static function prepare()
    {
        $cartsFolder = Tool::findOrCreateObjectFolder("/coreshop/carts/" . date("Y/m/d"));
        
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
        if(array_key_exists("shippingProvider", $session->order) && $session->order['deliveryProvider'] instanceof Plugin\Shipping) {
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

        if($cheapestProvider instanceof Plugin\Shipping)
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

    public function findItemForProduct(CoreShopProduct $product)
    {
        if (!$product instanceof CoreShopProduct)
            throw new \Exception("\$product must be instance of CoreShopProduct");

        foreach ($this->getItems() as $item){
            if($item->getProduct()->getId() == $product->getId())
                return $item;
        }

        return false;
    }

    public function updateQuantity(CoreShopProduct $product, $amount = 0, $autoAddCartRule = true)
    {
        if(!$product instanceof CoreShopProduct)
            throw new \Exception("\$product must be instance of CoreShopProduct");

        $item = $this->findItemForProduct($product);

        if($item instanceof CoreShopCartItem)
        {
            if($amount <= 0) {
                $this->removeItem($item);

                return false;
            }
            else {
                $item->setAmount($amount);
            }
        }
        else
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
        }

        if($autoAddCartRule)
            CoreShopCartRule::autoAddToCart();

        return $item;
    }

    public function addItem(CoreShopProduct $product, $amount = 1)
    {
        return $this->updateQuantity($product, $amount);
    }
    
    public function removeItem(CoreShopCartItem $item)
    {
        $item->delete();
    }
    
    public function modifyItem(CoreShopCartItem $item, $amount)
    {
        return $this->updateQuantity($item->getProduct(), $amount);
    }

    public function removeCartRule()
    {
        if($this->getCartRule() instanceof CoreShopCartRule)
        {
            if($this->getCartRule()->getFreeGift() instanceof CoreShopProduct)
            {
                $this->updateQuantity($this->getCartRule()->getFreeGift(), 0, false);
            }

            $this->setCartRule(null);
            $this->save();
        }

        return true;
    }

    public function addCartRule(CoreShopCartRule $cartRule)
    {
        $this->removeCartRule();
        $this->setCartRule($cartRule);

        if ($cartRule->getFreeGift() instanceof CoreShopProduct) {
            $item = $this->updateQuantity($cartRule->getFreeGift(), 1, false);

            $item->setIsGiftItem(true);
            $item->save();
        }

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
            "user" => $this->getUser() ? $this->getUser()->toArray() : null,
            "items" => $items,
            "subtotal" => Tool::formatPrice($this->getSubtotal()),
            "total" => Tool::formatPrice($this->getTotal())
        );
    }
}