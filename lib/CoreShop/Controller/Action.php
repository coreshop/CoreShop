<?php
    
namespace CoreShop\Controller;

use CoreShop\Plugin;
use CoreShop\Tool;

class Action extends \Website\Controller\Action {
    
    /*
        Zend_Session_Namespace
    */
    protected $cartSession;
    
    public function init()
    {
        parent::init();
        
        Plugin::getEventManager()->trigger('controller.init', $this);
        
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
        
        $this->session = $this->view->session = \Pimcore\Tool\Session::get('CoreShop');
        
        $this->enableLayout();
        $this->setLayout(Plugin::getLayout());
        
        $this->view->isShop = true;
    }
    
    public function preDispatch()
    {
        parent::preDispatch();
        
        $result = Plugin::getEventManager()->trigger('product.' . $this->getRequest()->getActionName(), $this, array("product" => $product, "cart" => $this->cart, "request" => $this->getRequest()), function($v) {
            return is_array($v) && array_key_exists("action", $v) && array_key_exists("controller", $v) && array_key_exists("module", $v);
        });

        if ($result->stopped()) {
            $forward = $result->last();

            $this->_forward($forward['action'], $forward['controller'], $forward['module'], $forward['params']);
        }
    }
    
    protected function prepareCart()
    {
        $this->cart = Tool::prepareCart();
    }
}
