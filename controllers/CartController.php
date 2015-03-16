<?php

class CoreShop_CartController extends CoreShop_Controller_Action {
    
    public function preDispatch()
    {
        parent::preDispatch();
        
        $this->prepareCart();
    }
    
    public function addAction () {
        $product_id = $this->getParam("product", null);
        $product = CoreShop_Product::getById($product_id);

        if($product instanceof CoreShop_Product)
        {
            $this->cart->addProduct($product);

            $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
        }
        
        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function removeAction() {
        $product_id = $this->getParam("product", null);
        $product = CoreShop_Product::getById($product_id);
        
        if($product instanceof CoreShop_Product)
        {
            $this->cart->removeProduct($product);

            $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
        }
        
        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function modifyAction() {
        
    }
}
