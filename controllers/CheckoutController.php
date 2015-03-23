<?php

class CoreShop_CheckoutController extends CoreShop_Controller_Action 
{
    public function preDispatch() {
        parent::preDispatch();
        
        if(count($this->view->cart->getItems()) == 0)
        {
            $this->_redirect($this->view->url(array("action" => "list"), "coreshop_cart"));
        }
        
        $this->prepareCart();
    }
    
    public function indexAction() {
        if($this->session->user instanceof Object_Concrete)
        {
            $this->_redirect($this->view->url(array("action" => "address"), "coreshop_checkout"));
        }
        
        $this->view->message = $this->getParam("message");
    }
    
    public function loginAction() {
        if($this->getRequest()->isPost())
        {
            $user = CoreShop_User::getUniqueByEmail($this->getParam("email"));

            if ($user instanceof Object_Concrete) {
                try {
                    $isAuthenticated = $user->authenticate($this->getParam("password"));
                    
                    if($isAuthenticated) {
                        $this->session->user = $user;
                        
                        $this->_redirect($this->view->url(array("action" => "address"), "coreshop_checkout"));
                    }
                }
                catch (Exception $ex) {
                    $this->view->message = $this->view->translate($ex->getMessage());
                }
            }
            else
                $this->view->message = $this->view->translate("User not found");
        }
        
        $this->_helper->viewRenderer("coreshop/checkout/index", null, true);
    }
    
    public function registerAction() {
        
    }
    
    public function addressAction() {
        if(!$this->session->user instanceof Object_Concrete)
        {
            $this->_redirect($this->view->url(array("action" => "index"), "coreshop_checkout"));
        }
    }
    
    public function deliveryAction() {
        if(!$this->session->user instanceof Object_Concrete) {
            $this->_redirect($this->view->url(array("action" => "index"), "coreshop_checkout"));
        }
        
        $this->view->provider = CoreShop::getDeliveryProvider($this->cart);
    }
    
    public function paymentAction() {
        
    }
}
