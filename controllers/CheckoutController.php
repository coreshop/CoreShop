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

use CoreShopTemplate\Controller\Action;

use CoreShop\Plugin;
use CoreShop\Model\Plugin\Shipping;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Tool;

use Pimcore\Model\Object\CoreShopOrder;
use Pimcore\Model\Object\CoreShopOrderState;
use Pimcore\Model\Object\CoreShopUser;
use Pimcore\Model\Object\Service;

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
        if($this->session->user instanceof CoreShopUser)
        {
            $this->_redirect($this->view->url(array("action" => "address"), "coreshop_checkout"));
        }
        
        $this->view->message = $this->getParam("message");
        
        $this->view->headTitle($this->view->translate("Checkout"));
    }
    
    public function registerAction() {
        
    }
    
    public function addressAction() {
        $this->checkIsAllowed();
        
        if($this->getRequest()->isPost())
        {
            $shippingAddress = $this->getParam("shipping-address");
            $billingAddress = $this->getParam("billing-address");
            
            if($this->getParam("useShippingAsBilling", "off") == "on")
            {
                $billingAddress = $this->getParam("shipping-address");
            }

            $this->session->order['address'] = array(
                "billing" => $billingAddress,
                "shipping" => $shippingAddress
            );
            
            $this->_redirect($this->view->url(array("action" => "shipping"), "coreshop_checkout"));
        }
        
        $this->view->headTitle($this->view->translate("Address"));
    }
    
    public function shippingAction() {
        $this->checkIsAllowed();
        
        
        //Download Article - no need for Shipping
        if(!$this->cart->hasPhysicalItems()) {
            $this->_redirect($this->view->url(array("action" => "payment"), "coreshop_checkout"));
        }
        
        $this->view->carriers = \CoreShop\Model\Carrier::getCarriersForCart($this->cart);
        
        if($this->getRequest()->isPost())
        {
            $shippingProvider = $this->getParam("carrier", false);
            
            foreach($this->view->carriers as $carrier)
            {
                if($carrier->getId() == $shippingProvider)
                {
                    $shippingProvider = $carrier;
                    break;
                }
            }
            
            if(!$carrier instanceof \CoreShop\Model\Carrier)
            {
                $this->view->error = "oh shit, not found";
            }
            else
            {
                $this->session->order['carrier'] = $carrier;
                
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
                $fieldCollectionShipping = new \Pimcore\Model\Object\Fieldcollection();
                $fieldCollectionShipping->add($this->session->user->findAddressByName($this->session->order['address']['shipping']));

                $fieldCollectionBilling = new \Pimcore\Model\Object\Fieldcollection();
                $fieldCollectionBilling->add($this->session->user->findAddressByName($this->session->order['address']['billing']));

                $this->session->order['paymentProvider'] = $provider;
                $order = new CoreShopOrder();
                $order->setKey(uniqid());
                $order->setOrderNumber(\CoreShop\Model\NumberRange::getNextNumberForType("order"));
                $order->setParent(Service::createFolderByPath('/coreshop/orders/' . date('Y/m/d')));
                $order->setPublished(true);
                $order->setLang($this->view->language);
                $order->setCustomer($this->session->user);
                $order->setShippingAddress($fieldCollectionShipping);
                $order->setBillingAddress($fieldCollectionBilling);
                $order->setPaymentProviderToken($provider->getIdentifier());
                $order->setPaymentProvider($provider->getName());
                $order->setPaymentProviderDescription($provider->getDescription());
                $order->setOrderDate(new \Zend_Date());

                if($this->session->order['carrier'] instanceof \CoreShop\Model\Carrier)
                {
                    $order->setCarrier($this->session->order['carrier']);
                    $order->setShipping($this->session->order['carrier']->getDeliveryPrice($this->cart));
                }
                else
                {
                    $order->setShipping(0);
                }

                $order->setTotal($this->cart->getTotal());
                $order->setSubtotal($this->cart->getSubtotal());
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
        if(!$this->session->user instanceof CoreShopUser) {
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

    public function errorAction()
    {

    }
    
    protected function checkIsAllowed()
    {
        if(!$this->session->user instanceof CoreShopUser) {
            $this->_redirect($this->view->url(array("action" => "index"), "coreshop_checkout"));
            exit;
        }
    }
}
