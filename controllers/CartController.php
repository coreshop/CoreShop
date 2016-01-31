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

use CoreShop\Plugin;
use CoreShopTemplate\Controller\Action;
use Pimcore\Model\Object\CoreShopCart;
use Pimcore\Model\Object\CoreShopCartItem;
use Pimcore\Model\Object\CoreShopProduct;
use CoreShop\Model\PriceRule;

class CoreShop_CartController extends Action
{
    
    public function init()
    {
        parent::init();
        
        $this->disableLayout();
    }
    
    public function preDispatch()
    {
        parent::preDispatch();

        //Cart is not allowed in CatalogMode
        if (\CoreShop\Config::isCatalogMode()) {
            $this->redirect($this->view->url(array(), "coreshop_index"));
        }

        $this->prepareCart();
    }
    
    public function addAction()
    {
        $product_id = $this->getParam("product", null);
        $amount = $this->getParam("amount", 1);
        $product = CoreShopProduct::getById($product_id);

        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('cart.preAdd', $this, array("product" => $product, "cart" => $this->cart, "request" => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }

        if ($isAllowed) {
            if ($product instanceof CoreShopProduct && $product->getEnabled() && $product->getAvailableForOrder()) {
                $item = $this->cart->addItem($product, $amount);
                
                Plugin::getEventManager()->trigger('cart.postAdd', $this, array("request" => $this->getRequest(), "product" => $product, "cart" => $this->cart, "cartItem" => $item));
                
                $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
            }
        } else {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }

        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function removeAction()
    {
        $cartItem = $this->getParam("cartItem", null);
        $item = CoreShopCartItem::getById($cartItem);
        
        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('cart.preRemove', $this, array("cartItem" => $item, "cart" => $this->cart, "request" => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }
        
        unset($this->session->order);
        
        if ($isAllowed) {
            if ($item instanceof CoreShopCartItem) {
                $this->cart->removeItem($item);
                
                Plugin::getEventManager()->trigger('cart.postRemove', $this, array("item" => $item, "cart" => $this->cart));
                
                $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
            }
        } else {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }
        
        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function modifyAction()
    {
        $cartItem = $this->getParam("cartItem", null);
        $amount = $this->getParam("amount");
        $item = CoreShopCartItem::getById($cartItem);
        
        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('cart.preModify', $this, array("cartItem" => $item, "cart" => $this->cart, "request" => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }
        
        unset($this->session->order);
        
        if ($isAllowed) {
            if ($item instanceof CoreShopCartItem) {
                $this->cart->modifyItem($item, $amount);
                
                Plugin::getEventManager()->trigger('cart.postModify', $this, array("item" => $item, "cart" => $this->cart));
                
                $this->_helper->json(array("success" => true, "cart" => $this->cart->toArray()));
            }
        } else {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }
        
        $this->_helper->json(array("success" => false, "cart" => $this->cart->toArray()));
    }
    
    public function listAction()
    {
        $this->enableLayout();

        $this->view->headTitle($this->view->translate("Cart"));
    }

    public function priceruleAction()
    {
        $this->enableLayout();

        if ($this->getRequest()->isPost()) {
            $priceRule = PriceRule::getByCode($this->getParam("priceRule"));

            if ($priceRule instanceof PriceRule) {
                if ($priceRule->checkValidity()) {
                    $this->cart->addPriceRule($priceRule);
                } else {
                    die("not valid");
                }
            } else {
                die("not found");
            }
        }

        $this->_redirect($this->getParam("redirect") ? $this->getParam("redirect") : $this->view->url(array("action" => "list"), "coreshop_cart"));
    }

    public function removepriceruleAction()
    {
        $this->enableLayout();

        $this->cart->removePriceRule();

        $this->_redirect($this->view->url(array("action" => "list"), "coreshop_cart"));
    }
}
