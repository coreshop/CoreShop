<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action;
use CoreShop\Plugin;
use CoreShop\Model\Plugin\Payment;

/**
 * Class CoreShop_CheckoutController
 */
class CoreShop_CheckoutController extends Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        //Checkout is not allowed in CatalogMode
        if (\CoreShop\Model\Configuration::isCatalogMode()) {
            $this->redirect($this->view->url(array(), 'coreshop_index'));
        }

        if (count($this->view->cart->getItems()) == 0 && $this->getParam('action') != 'thankyou') {
            $this->_redirect($this->view->url(array('act' => 'list'), 'coreshop_cart'));
        }

        if (!is_array($this->session->order)) {
            $this->session->order = array();
        }

        $this->prepareCart();
    }

    public function indexAction()
    {
        $user = CoreShop\Tool::getUser();

        if ($user instanceof \CoreShop\Model\User) {
            $this->_redirect($this->view->url(array('act' => 'address'), 'coreshop_checkout'));
        }

        if ($this->getParam('error')) {
            $this->view->error = $this->getParam('error');
        }

        $this->view->message = $this->getParam('message');

        $this->view->headTitle($this->view->translate('Checkout'));

        \CoreShop\Tracking\TrackingManager::getInstance()->trackCheckout($this->cart, 2);
    }

    public function registerAction()
    {
        $this->view->redirect = $this->view->url(array('lang' => $this->view->language, 'act' => 'address'), 'coreshop_checkout');

        $this->_helper->viewRenderer('user/register', null, true);
    }

    public function addressAction()
    {
        $this->checkIsAllowed();

        $user = \CoreShop::getTools()->getUser();

        if ($this->getRequest()->isPost()) {
            $shippingAddress = $this->getParam('shipping-address');
            $billingAddress = $this->getParam('billing-address');

            if ($this->getParam('useShippingAsBilling', 'off') == 'on') {
                $billingAddress = $this->getParam('shipping-address');
            }

            $fieldCollectionShipping = new \Pimcore\Model\Object\Fieldcollection();
            $fieldCollectionShipping->add($user->findAddressByName($shippingAddress));

            $fieldCollectionBilling = new \Pimcore\Model\Object\Fieldcollection();
            $fieldCollectionBilling->add($user->findAddressByName($billingAddress));

            $this->cart->setShippingAddress($fieldCollectionShipping);
            $this->cart->setBillingAddress($fieldCollectionBilling);
            $this->cart->save();

            //Reset Country in Session, now we use BillingAddressCountry
            unset($this->session->countryId);

            $this->_redirect($this->view->url(array('act' => 'shipping'), 'coreshop_checkout'));
        }

        \CoreShop\Tracking\TrackingManager::getInstance()->trackCheckout($this->cart, 3);
        $this->view->headTitle($this->view->translate('Address'));
    }

    public function shippingAction()
    {
        $this->checkIsAllowed();

        $this->view->message = $this->getParam('message');

        //Download Article - no need for Shipping
        if (!$this->cart->hasPhysicalItems()) {
            $this->_redirect($this->view->url(array('act' => 'payment'), 'coreshop_checkout'));
        }

        $this->view->carriers = \CoreShop\Model\Carrier::getCarriersForCart($this->cart);

        if ($this->getRequest()->isPost()) {
            if (!$this->getParam('termsAndConditions', false)) {
                $this->_redirect($this->view->url(array('act' => 'shipping', 'message' => 'Please check terms and conditions'), 'coreshop_checkout'));
            }

            $carrier = $this->getParam('carrier', false);

            foreach ($this->view->carriers as $c) {
                if ($c->getId() == $carrier) {
                    $carrier = $c;
                    break;
                }
            }

            if (!$carrier instanceof \CoreShop\Model\Carrier) {
                $this->view->error = 'oh shit, not found';
            } else {
                $this->cart->setCarrier($carrier);
                $this->cart->setPaymentModule(null); //Reset PaymentModule, payment could not be available for this carrier
                $this->cart->save();

                $this->_redirect($this->view->url(array('act' => 'payment'), 'coreshop_checkout'));
            }
        }

        \CoreShop\Tracking\TrackingManager::getInstance()->trackCheckout($this->cart, 4);
        $this->view->headTitle($this->view->translate('Shipping'));
    }

    public function paymentAction()
    {
        $this->checkIsAllowed();

        $this->view->provider = Plugin::getPaymentProviders($this->cart);

        if ($this->getRequest()->isPost()) {
            $paymentProvider = $this->getParam('payment_provider', array());
            $provider = null;

            foreach ($this->view->provider as $provider) {
                if ($provider->getIdentifier() == $paymentProvider) {
                    $paymentProvider = $provider;
                    break;
                }
            }

            if (!$paymentProvider instanceof Payment) {
                $this->view->error = 'oh shit, not found';
            } else {
                $this->cart->setPaymentModule($paymentProvider->getIdentifier());
                $this->cart->save();

                $this->redirect($paymentProvider->process($this->cart));
            }
        }

        \CoreShop\Tracking\TrackingManager::getInstance()->trackCheckout($this->cart, 5);
        $this->view->headTitle($this->view->translate('Payment'));
    }

    public function validateAction()
    {
        $this->view->headTitle($this->view->translate('Validate'));

        $paymentViewScript = $this->getParam("paymentViewScript");

        $this->view->paymentViewScript = $paymentViewScript;

        \CoreShop\Tracking\TrackingManager::getInstance()->trackCheckoutAction($this->cart, 6);
    }

    public function confirmationAction()
    {
        $this->view->headTitle($this->view->translate('Confirmation'));

        $order = $this->getParam("order");
        $paymentViewScript = $this->getParam("paymentViewScript");

        $this->prepareCart();
        //$this->cart->delete(); //Keep Cart for Statistics Purpose

        if (!$order instanceof \CoreShop\Model\Order) {
            $this->redirect('/'.$this->view->language);
        }

        $this->view->order = $order;
        $this->view->paymentViewScript = $paymentViewScript;

        unset($this->session->order);
        unset($this->session->cart);
        unset($this->session->cartId);

        if (CoreShop\Tool::getUser()->getIsGuest()) {
            \CoreShop::getTools()->unsetUser();
        }

        \CoreShop\Tracking\TrackingManager::getInstance()->trackCheckoutComplete($order);
    }

    public function errorAction()
    {
        $this->view->headTitle("Payment Error");
    }

    protected function checkIsAllowed()
    {
        if (!\CoreShop::getTools()->getUser() instanceof \CoreShop\Model\User) {
            $this->_redirect($this->view->url(array('act' => 'index'), 'coreshop_checkout'));
            exit;
        }
    }
}
