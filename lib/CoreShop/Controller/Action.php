<?php

class CoreShop_Controller_Action extends Website_Controller_Action {
    
    /*
        Zend_Session_Namespace
    */
    protected $cartSession;
    
    public function init()
    {
        parent::init();
        
        CoreShop::getEventManager()->trigger('controller.init', $this);
        
        $this->view->setScriptPath(
            array_merge(
                $this->view->getScriptPaths(),
                array(
                    PIMCORE_WEBSITE_PATH . '/views/scripts/',
                    PIMCORE_WEBSITE_PATH . '/views/layouts/',
                    PIMCORE_WEBSITE_PATH . '/views/scripts/coreshop/'
                )
            )
        );
        
        $this->session = $this->view->session = Pimcore_Tool_Session::get('CoreShop');
        
        $this->setLayout(CoreShop::getLayout());
    }
    
    public function preDispatch()
    {
        parent::preDispatch();
        
        $result = CoreShop::getEventManager()->trigger('product.' . $this->getRequest()->getActionName(), $this, array("product" => $product, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_array($v) && array_key_exists("action", $v) && array_key_exists("controller", $v) && array_key_exists("module", $v);
        });

        if ($result->stopped()) {
            $forward = $result->last();
            
            $this->_forward($forward['action'], $forward['controller'], $forward['module'], $forward['params']);
        }
    }
    
    protected function prepareCart()
    {
        $this->cart = CoreShop_Tool::prepareCart();
    }
}
