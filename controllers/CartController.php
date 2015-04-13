<?php

use CoreShop;
use CoreShop\Plugin;
use CoreShop\Cart;
use CoreShop\CartItem;
use CoreShop\Product;
use CoreShop\Controller\Action;

class CoreShop_CartController extends Action {
    
    public function init()
    {
        parent::init();
        
        $this->disableLayout();
    }
    
    public function preDispatch()
    {
        parent::preDispatch();
        
        $this->prepareCart();
    }
    
    public function addAction () 
    {
        $product_id = $this->getParam("product", null);
        $amount = $this->getParam("amount", 1);
        $product = Product::getById($product_id);

        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('cart.preAdd', $this, array("product" => $product, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }

        if($isAllowed)
        {
            if($product instanceof Product && $product->getEnabled() && $product->getAvailableForOrder())
            {
                $item = $this->cart->addItem($product, $amount);
                
                Plugin::getEventManager()->trigger('cart.postAdd', $this, array("request" => $this->getRequest(), "product" => $product, "cart" => $this->cart, "cartItem" => $item));
                
                $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
            }
        }
        else
        {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }

        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function removeAction() {
        $cartItem = $this->getParam("cartItem", null);
        $item = CartItem::getById($cartItem);
        
        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('cart.preRemove', $this, array("cartItem" => $item, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }
        
        unset($this->session->order);
        
        if($isAllowed)
        {
            if($item instanceof CartItem)
            {
                $this->cart->removeItem($item);
                
                Plugin::getEventManager()->trigger('cart.postRemove', $this, array("item" => $item, "cart" => $this->cart));
                
                $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
            }
        }
        else
        {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }
        
        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function modifyAction() {
        $cartItem = $this->getParam("cartItem", null);
        $amount = $this->getParam("amount");
        $item = CartItem::getById($cartItem);
        
        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('cart.preModify', $this, array("cartItem" => $item, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }
        
        unset($this->session->order);
        
        if($isAllowed)
        {
            if($item instanceof CartItem)
            {
                $this->cart->modifyItem($item, $amount);
                
                Plugin::getEventManager()->trigger('cart.postModify', $this, array("item" => $item, "cart" => $this->cart));
                
                $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
            }
        }
        else
        {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }
        
        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function listAction() {
        $this->enableLayout();

        $this->view->headTitle($this->view->translate("Cart"));
    }
}
