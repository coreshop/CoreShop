<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action;
use CoreShop\Plugin;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Tool;
use Pimcore\Model\Object\CoreShopOrder;
use Pimcore\Model\Object\CoreShopUser;

class CoreShop_CheckoutController extends Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        //Checkout is not allowed in CatalogMode
        if (\CoreShop\Config::isCatalogMode()) {
            $this->redirect($this->view->url(array(), "coreshop_index"));
        }
        
        if (count($this->view->cart->getItems()) == 0 && $this->getParam("action") != "thankyou") {
            $this->_redirect($this->view->url(array("act" => "list"), "coreshop_cart"));
        }
        
        if (!is_array($this->session->order)) {
            $this->session->order = array();
        }
        
        $this->prepareCart();
    }
    
    public function indexAction()
    {
        if ($this->session->user instanceof CoreShopUser) {
            $this->_redirect($this->view->url(array("act" => "address"), "coreshop_checkout"));
        }

        if($this->getParam("error")) {
            $this->view->error = $this->getParam("error");
        }

        $this->view->message = $this->getParam("message");
        
        $this->view->headTitle($this->view->translate("Checkout"));
    }
    
    public function registerAction()
    {
        $this->view->redirect = $this->view->url(array("lang" => $this->view->language, "act" => "address"), "coreshop_checkout");

        $this->_helper->viewRenderer('user/register', null, true);
    }
    
    public function addressAction()
    {
        $this->checkIsAllowed();
        
        if ($this->getRequest()->isPost()) {
            $shippingAddress = $this->getParam("shipping-address");
            $billingAddress = $this->getParam("billing-address");
            
            if ($this->getParam("useShippingAsBilling", "off") == "on") {
                $billingAddress = $this->getParam("shipping-address");
            }

            $fieldCollectionShipping = new \Pimcore\Model\Object\Fieldcollection();
            $fieldCollectionShipping->add($this->session->user->findAddressByName($shippingAddress));

            $fieldCollectionBilling = new \Pimcore\Model\Object\Fieldcollection();
            $fieldCollectionBilling->add($this->session->user->findAddressByName($billingAddress));

            $this->cart->setShippingAddress($fieldCollectionShipping);
            $this->cart->setBillingAddress($fieldCollectionBilling);
            $this->cart->save();

            //Reset Country in Session, now we use BillingAddressCountry
            unset($this->session->countryId);

            $this->_redirect($this->view->url(array("act" => "shipping"), "coreshop_checkout"));
        }
        
        $this->view->headTitle($this->view->translate("Address"));
    }
    
    public function shippingAction()
    {
        $this->checkIsAllowed();

        $this->view->message = $this->getParam("message");
        
        //Download Article - no need for Shipping
        if (!$this->cart->hasPhysicalItems()) {
            $this->_redirect($this->view->url(array("act" => "payment"), "coreshop_checkout"));
        }
        
        $this->view->carriers = \CoreShop\Model\Carrier::getCarriersForCart($this->cart);
        
        if ($this->getRequest()->isPost()) {
            if(!$this->getParam("termsAndConditions", false)) {
                $this->_redirect($this->view->url(array("act" => "shipping", "message" => "Please check terms and conditions"), "coreshop_checkout"));
            }

            $carrier = $this->getParam("carrier", false);
            
            foreach ($this->view->carriers as $c) {
                if ($c->getId() == $carrier) {
                    $carrier = $c;
                    break;
                }
            }
            
            if (!$carrier instanceof \CoreShop\Model\Carrier) {
                $this->view->error = "oh shit, not found";
            } else {
                $this->cart->setCarrier($carrier);
                $this->cart->setPaymentModule(null); //Reset PaymentModule, payment could not be available for this carrier
                $this->cart->save();

                $this->_redirect($this->view->url(array("act" => "payment"), "coreshop_checkout"));
            }
        }
        
        $this->view->headTitle($this->view->translate("Shipping"));
    }

    public function paymentAction()
    {
        $this->checkIsAllowed();

        $this->view->provider = Plugin::getPaymentProviders($this->cart);

        if ($this->getRequest()->isPost()) {
            $paymentProvider = $this->getParam("payment_provider", array());
            $provider = null;

            foreach ($this->view->provider as $provider) {
                if ($provider->getIdentifier() == $paymentProvider) {
                    $paymentProvider = $provider;
                    break;
                }
            }

            if (!$paymentProvider instanceof Payment) {
                $this->view->error = "oh shit, not found";
            } else {
                $this->cart->setPaymentModule($paymentProvider->getIdentifier());
                $this->cart->save();

                $this->redirect($paymentProvider->process($this->cart));
            }
        }

        $this->view->headTitle($this->view->translate("Payment"));
    }

    public function thankyouAction()
    {
        if (!$this->session->user instanceof CoreShopUser) {
            $this->_redirect($this->view->url(array("act" => "index"), "coreshop_checkout"));
            exit;
        }

        $this->view->order = CoreShopOrder::getById($this->session->orderId);


        if (!$this->view->order instanceof CoreShopOrder) {
            $this->_redirect("/" . $this->language . "/shop");
        }

        $this->cart->delete();
        $this->prepareCart();
        
        unset($this->session->order);
        unset($this->session->cart);

        if ($this->session->user->getIsGuest()) {
            unset($this->session->user);
        }
        
        $this->view->headTitle($this->view->translate("Thank you"));
    }

    public function errorAction()
    {
    }
    
    protected function checkIsAllowed()
    {
        if (!$this->session->user instanceof CoreShopUser) {
            $this->_redirect($this->view->url(array("act" => "index"), "coreshop_checkout"));
            exit;
        }
    }
}
