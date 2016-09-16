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
use CoreShop\Model\Cart\PriceRule;

/**
 * Class CoreShop_CartController
 */
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
        if (\CoreShop\Model\Configuration::isCatalogMode()) {
            $this->redirect(\CoreShop::getTools()->url(array(), 'coreshop_index'));
        }

        $this->prepareCart();
    }

    public function addAction()
    {
        $product_id = $this->getParam('product', null);
        $amount = $this->getParam('amount', 1);
        $product = \CoreShop\Model\Product::getById($product_id);
        $isAllowed = true;
        $message = 'is not allowed';

        if (!$product->isAvailableWhenOutOfStock() && $product->getQuantity() <= 0) {
            $isAllowed = false;

            $message = $this->view->translate('Product is out of stock');
        }

        $result = \Pimcore::getEventManager()->trigger('coreshop.cart.preAdd', $this, array('product' => $product, 'cart' => $this->cart, 'request' => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }

        if ($isAllowed) {
            if ($product instanceof \CoreShop\Model\Product && $product->getEnabled() && $product->getAvailableForOrder()) {
                $item = $this->cart->addItem($product, $amount);

                \Pimcore::getEventManager()->trigger('coreshop.cart.postAdd', $this, array('request' => $this->getRequest(), 'product' => $product, 'cart' => $this->cart, 'cartItem' => $item));

                $this->_helper->json(array('success' => true, 'cart' => $this->renderCart(), 'minicart' => $this->renderMiniCart()));
            }
        } else {
            $this->_helper->json(array('success' => false, 'message' => $message));
        }

        $this->_helper->json(array('success' => false, 'cart' => $this->renderCart(), 'minicart' => $this->renderMiniCart()));
    }

    public function removeAction()
    {
        $cartItem = $this->getParam('cartItem', null);
        $item = \CoreShop\Model\Cart\Item::getById($cartItem);

        $isAllowed = true;
        $result = \Pimcore::getEventManager()->trigger('coreshop.cart.preRemove', $this, array('cartItem' => $item, 'cart' => $this->cart, 'request' => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }

        unset($this->session->order);

        if ($isAllowed) {
            if ($item instanceof \CoreShop\Model\Cart\Item) {
                $this->cart->removeItem($item);
                $this->reloadCart();

                \Pimcore::getEventManager()->trigger('coreshop.cart.postRemove', $this, array('item' => $item, 'cart' => $this->cart));

                $this->_helper->json(array('success' => true, 'cart' => $this->renderCart(), 'minicart' => $this->renderMiniCart()));
            }
        } else {
            $this->_helper->json(array('success' => false, 'message' => 'not allowed'));
        }

        $this->reloadCart();

        $this->_helper->json(array('success' => false, 'cart' => $this->renderCart(), 'minicart' => $this->renderMiniCart()));
    }

    public function modifyAction()
    {
        $cartItem = $this->getParam('cartItem', null);
        $amount = $this->getParam('amount');
        $item = \CoreShop\Model\Cart\Item::getById($cartItem);

        $isAllowed = true;
        $result = \Pimcore::getEventManager()->trigger('coreshop.cart.preModify', $this, array('cartItem' => $item, 'cart' => $this->cart, 'request' => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }

        unset($this->session->order);

        if ($isAllowed) {
            if ($item instanceof \CoreShop\Model\Cart\Item) {
                $this->cart->modifyItem($item, $amount);

                \Pimcore::getEventManager()->trigger('coreshop.cart.postModify', $this, array('item' => $item, 'cart' => $this->cart));

                $this->_helper->json(array('success' => true, 'cart' => $this->renderCart(), 'minicart' => $this->renderMiniCart()));
            }
        } else {
            $this->_helper->json(array('success' => false, 'message' => 'not allowed'));
        }

        $this->_helper->json(array('success' => false, 'cart' => $this->renderCart(), 'minicart' => $this->renderMiniCart()));
    }

    public function listAction()
    {
        $this->enableLayout();

        $this->view->headTitle($this->view->translate('Cart'));
    }

    public function priceruleAction()
    {
        $this->enableLayout();

        $error = false;

        if ($this->getRequest()->isPost()) {
            $priceRule = PriceRule::getByCode($this->getParam('priceRule'));

            if ($priceRule instanceof PriceRule) {
                if ($priceRule->checkValidity($this->cart, $this->getParam('priceRule'))) {
                    $this->cart->addPriceRule($priceRule, $this->getParam('priceRule'));
                } else {
                    $error = $this->view->translate('Voucher is invalid');
                }
            } else {
                $error = $this->view->translate('Voucher is invalid');
            }
        }

        $this->_redirect($this->getParam('redirect') ? $this->getParam('redirect').'?error='.$error : \CoreShop::getTools()->url(array('act' => 'list', 'error' => $error), 'coreshop_cart', true));
    }

    public function removepriceruleAction()
    {
        $this->enableLayout();

        $id = $this->getParam("id");

        foreach ($this->cart->getPriceRules() as $ruleItem) {
            if ($ruleItem->getPriceRule() instanceof PriceRule && $ruleItem->getPriceRule()->getId() === $id) {
                $this->cart->removePriceRule($ruleItem->getPriceRule());
            }
        }

        $this->_redirect(\CoreShop::getTools()->url(array('act' => 'list'), 'coreshop_cart'));
    }

    /**
     * @return string
     *
     * @throws Zend_Exception
     */
    protected function renderMiniCart()
    {
        return $this->renderCartView('coreshop/cart/helper/minicart.php');
    }

    /**
     * @return string
     */
    protected function renderCart()
    {
        return $this->renderCartView('coreshop/cart/helper/cart.php');
    }

    /**
     * @param $view
     *
     * @return string
     *
     * @throws Zend_Exception
     */
    protected function renderCartView($view)
    {
        return $this->view->partial($view, array(
            'cart' => $this->cart,
            'language' => (string) \Zend_Registry::get('Zend_Locale'),
            'edit' => true,
        ));
    }

    protected function reloadCart()
    {
        \Zend_Registry::set('object_'.$this->cart->getId(), null);
        $this->prepareCart();
    }
}
