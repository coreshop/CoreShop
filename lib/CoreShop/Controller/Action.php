<?php

class CoreShop_Controller_Action extends Pimcore_Controller_Action_Frontend {
    
    /*
        Zend_Session_Namespace
    */
    protected $cartSession;
    
    public function init()
    {
        parent::init();
    }
    
    protected function prepareCart()
    {
        $this->cart = CoreShop_Tool::prepareCart();
    }
}
