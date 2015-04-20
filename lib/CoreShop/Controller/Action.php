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

namespace CoreShop\Controller;

use CoreShop\Plugin;
use CoreShop\Tool;

use Pimcore\Model\Object\CoreShopCountry;
use Pimcore\Model\Object\CoreShopCurrency;
use Pimcore\Model\Object\CoreShopCartRule;

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

        $this->view->addHelperPath(CORESHOP_PATH . '/lib/CoreShop/View/Helper', 'CoreShop\View\Helper');

        $this->session = $this->view->session = Tool::getSession();

        /*
        if(!$this->session->country instanceof CoreShopCountry) {
            if($this->session->user instanceof \CoreShop\Plugin\User && count($this->session->user->getAddresses()) > 0)
            {
                $this->session->country = $this->session->user->getAddresses()->get(0)->getCountry();
            }
            else
                $this->session->country = Tool::getCountry();
        }
        */

        if($this->getParam("currency"))
        {
            if(CoreShopCurrency::getById($this->getParam("currency")) instanceof CoreShopCurrency)
                $this->session->currencyId = $this->getParam("currency");
        }

        $this->view->country = Tool::getCountry();

        $this->enableLayout();
        $this->setLayout(Plugin::getLayout());

        $this->prepareCart();

        $this->view->isShop = true;
    }
    
    public function preDispatch()
    {
        parent::preDispatch();

        $result = Plugin::getEventManager()->trigger('action.' . $this->getRequest()->getActionName(), $this, array("controller" => $this, "request" => $this->getRequest()), function($v) {
            return is_array($v) && array_key_exists("action", $v) && array_key_exists("controller", $v) && array_key_exists("module", $v);
        });

        if ($result->stopped()) {
            $forward = $result->last();

            $this->_forward($forward['action'], $forward['controller'], $forward['module'], $forward['params']);
        }
    }
    
    protected function prepareCart()
    {
        $this->cart = $this->view->cart = Tool::prepareCart();

        if($this->session->user instanceof Plugin\User && !$this->cart->getUser() instanceof Plugin\User)
        {
            $this->cart->setUser($this->session->user);
            $this->cart->save();
        }

        CoreShopCartRule::autoRemoveFromCart($this->cart);
        CoreShopCartRule::autoAddToCart($this->cart);
    }
}
