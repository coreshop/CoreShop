<?php

class CoreShop_CartController extends CoreShop_Controller_Action {
    
    public function preDispatch()
    {
        parent::preDispatch();
        
        $this->prepareCart();
    }
    
    public function addAction () 
    {
        $product_id = $this->getParam("product", null);
        $amount = $this->getParam("amount", 1);
        $product = CoreShop_Product::getById($product_id);
        
        $isAllowed = true;
        $result = CoreShop::getEventManager()->trigger('cart.preAdd', $this, array("product" => $product, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }
        
        if($isAllowed)
        {
            if($product instanceof CoreShop_Product)
            {
                $item = $this->cart->addItem($product, $amount);
                
                CoreShop::getEventManager()->trigger('cart.postAdd', $this, array("request" => $this->getRequest(), "product" => $product, "cart" => $this->cart, "cartItem" => $item));
                
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
        $item = CoreShop_CartItem::getById($cartItem);
        
        $isAllowed = true;
        $result = CoreShop::getEventManager()->trigger('cart.preRemove', $this, array("cartItem" => $item, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }
        
        if($isAllowed)
        {
            if($item instanceof CoreShop_CartItem)
            {
                $this->cart->removeItem($item);
                
                CoreShop::getEventManager()->trigger('cart.postRemove', $this, array("item" => $item, "cart" => $this->cart));
                
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
        $item = CoreShop_CartItem::getById($cartItem);
        
        $isAllowed = true;
        $result = CoreShop::getEventManager()->trigger('cart.preModify', $this, array("cartItem" => $item, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }
        
        if($isAllowed)
        {
            if($item instanceof CoreShop_CartItem)
            {
                $this->cart->modifyItem($item, $amount);
                
                CoreShop::getEventManager()->trigger('cart.postModify', $this, array("item" => $item, "cart" => $this->cart));
                
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
        
    }
}
