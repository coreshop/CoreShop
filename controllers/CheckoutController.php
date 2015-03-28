<?php
    
use CoreShop;
use CoreShop\Controller\Action;

use CoreShop\Interface\Delivery;
use CoreShop\Interface\Payment;
use CoreShop\Tool;

use Object\CoreShopOrder;

class CoreShop_CheckoutController extends Action 
{
    public function preDispatch() {
        parent::preDispatch();
        
        if(count($this->view->cart->getItems()) == 0 && $this->getParam("action") != "thankyou")
        {
            $this->_redirect($this->view->url(array("action" => "list"), "coreshop_cart"));
        }
        
        if(!is_array($this->session->order))
        {
            $this->session->order = array();
        }
        
        $this->prepareCart();
    }
    
    public function indexAction() {
        if($this->session->user instanceof Object\Concrete)
        {
            $this->_redirect($this->view->url(array("action" => "address"), "coreshop_checkout"));
        }
        
        $this->view->message = $this->getParam("message");
        
        $this->view->headTitle($this->view->translate("Checkout"));
    }
    
    public function loginAction() {
        if($this->getRequest()->isPost())
        {
            $user = User::getUniqueByEmail($this->getParam("email"));

            if ($user instanceof Object\Concrete) {
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
        
        $this->view->headTitle($this->view->translate("Login"));
    }
    
    public function registerAction() {
        
    }
    
    public function addressAction() {
        $this->checkIsAllowed();
        
        if($this->getRequest()->isPost())
        {
            $deliveryAddress = $this->getParam("deliveryAddress");
            $billingAddress = $this->getParam("billingAddress");
            
            if($this->getParam("useDeliveryAsBilling", "off") == "on")
            {
                $billingAddress = $this->getParam("deliveryAddress");
            }

            $this->session->order['address'] = array(
                "billing" => $billingAddress,
                "delivery" => $deliveryAddress
            );
            
            $this->_redirect($this->view->url(array("action" => "delivery"), "coreshop_checkout"));
        }
        
        $this->view->headTitle($this->view->translate("Address"));
    }
    
    public function deliveryAction() {
        $this->checkIsAllowed();
        
        
        //Download Article - no need for Delivery
        if(!$this->cart->hasPhysicalItems()) {
            $this->_redirect($this->view->url(array("action" => "payment"), "coreshop_checkout"));
        }
        
        $this->view->provider = Plugin::getDeliveryProviders($this->cart);
        
        if($this->getRequest()->isPost())
        {
            $deliveryProvider = reset($this->getParam("delivery_provider", array()));
            
            foreach($this->view->provider as $provider)
            {
                if($provider->getIdentifier() == $deliveryProvider)
                {
                    $deliveryProvider = $provider;
                    break;
                }
            }
            
            if(!$provider instanceof Delivery)
            {
                $this->view->error = "oh shit, not found";
            }
            else
            {
                $this->session->order['deliveryProvider'] = $provider;
                
                $this->_redirect($this->view->url(array("action" => "payment"), "coreshop_checkout"));
            }
        }
        
        $this->view->headTitle($this->view->translate("Shipping"));
    }
    
    public function paymentAction() {
        $this->checkIsAllowed();

        $this->view->provider = Plugin::getPaymentProviders($this->cart);

        if($this->getRequest()->isPost())
        {
            $paymentProvider = reset($this->getParam("payment_provider", array()));

            foreach($this->view->provider as $provider)
            {
                if($provider->getIdentifier() == $$paymentProvider)
                {
                    $paymentProvider = $provider;
                    break;
                }
            }
            
            if(!$provider instanceof Payment)
            {
                $this->view->error = "oh shit, not found";
            }
            else
            {
                $this->session->order['paymentProvider'] = $provider;

                $order = new CoreShopOrder();
                $order->setKey(uniqid());
                $order->setParent(Tool::findOrCreateObjectFolder("/coreshop/orders/".date('Y-m-d')));
                $order->setPublished(true);
                $order->setLang($this->view->language);
                $order->setCustomer($this->session->user);
                $order->setDeliveryAddress($this->session->order['address']['delivery']);
                $order->setBillingAddress($this->session->order['address']['billing']);
                $order->setPaymentProvider($provider->getIdentifier());
                
                if($this->session->order['deliveryProvider'] instanceof Delivery)
                {
                    $order->setDeliveryProvider($this->session->order['deliveryProvider']->getIdentifier());
                    $order->setDeliveryFee($this->session->order['deliveryProvider']->getDeliveryFee($this->cart));
                }
                else
                {
                    $order->setDeliveryFee(0);
                }
                
                $order->save();
                
                $order->importCart($this->cart);
                
                $this->session->orderId = $order->getId();

                $this->_helper->viewRenderer($provider->processPayment($order, $this->view->url(array("action" => "paymentreturn"), "coreshop_checkout")), null, true);
            }
        }
        
        $this->view->headTitle($this->view->translate("Payment"));
    }

    public function thankyouAction()
    {
        if(!$this->session->user instanceof Object\Concrete) {
            $this->_redirect($this->view->url(array("action" => "index"), "coreshop_checkout"));
            exit;
        }

        $this->view->order = CoreShopOrder::getById($this->session->orderId);
        
        if(!$this->view->order instanceof CoreShopOrder)
            $this->_redirect("/" . $this->language . "/shop");
        
        $this->cart->delete();
        $this->prepareCart();
        
        unset($this->session->order);
        unset($this->session->cart);
        
        $this->view->headTitle($this->view->translate("Thank you"));
    }
    
    protected function checkIsAllowed()
    {
        if(!$this->session->user instanceof Object\Concrete) {
            $this->_redirect($this->view->url(array("action" => "index"), "coreshop_checkout"));
            exit;
        }
    }
}
