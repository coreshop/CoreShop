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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action;
use CoreShop\Model\Cart\PriceRule;

/**
 * Class CoreShop_CartListController
 */
class CoreShop_CartListController extends Action
{
    public function init()
    {
        parent::init();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!\CoreShop::getTools()->getUser() instanceof \CoreShop\Model\User) {
            $this->redirect(\CoreShop::getTools()->url(['lang' => $this->language], 'coreshop_index'));
            exit;
        }

        //CartList is not allowed in CatalogMode
        if (\CoreShop\Model\Configuration::isCatalogMode()) {
            $this->redirect(\CoreShop::getTools()->url([], 'coreshop_index'));
        }

        $this->prepareCart();
    }

    public function listAction()
    {
        $this->view->carts = \CoreShop::getTools()->getCartManager()->getCarts(\CoreShop::getTools()->getUser());
    }

    public function detailAction()
    {
        $cartId = $this->getParam("id");
        $cart = \CoreShop\Model\Cart::getById($cartId);

        if (!$cart instanceof \CoreShop\Model\Cart) {
            $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
        }

        if (!$cart->getUser() instanceof \CoreShop\Model\User || $cart->getUser()->getId() != \CoreShop::getTools()->getUser()->getId()) {
            $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
        }

        $this->view->cart = $cart;
    }

    public function activateAction()
    {
        $cartId = $this->getParam("id");
        $cart = \CoreShop\Model\Cart::getById($cartId);

        if (!$cart instanceof \CoreShop\Model\Cart) {
            $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
        }

        if (!$cart->getUser() instanceof \CoreShop\Model\User || $cart->getUser()->getId() != \CoreShop::getTools()->getUser()->getId()) {
            $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
        }

        \CoreShop::getTools()->getCartManager()->setSessionCart($cart);

        $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
    }

    public function saveAction()
    {
        \CoreShop::getTools()->getCartManager()->setSessionCart(\CoreShop::getTools()->getCartManager()->createCart("default", \CoreShop::getTools()->getUser()));

        $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
    }

    public function deleteAction()
    {
        $cartId = $this->getParam("id");
        $cart = \CoreShop\Model\Cart::getById($cartId);

        if (!$cart instanceof \CoreShop\Model\Cart) {
            $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
        }

        if (!$cart->getUser() instanceof \CoreShop\Model\User || $cart->getUser()->getId() != \CoreShop::getTools()->getUser()->getId()) {
            $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
        }

        \CoreShop::getTools()->getCartManager()->deleteCart($cart);

        $this->redirect(\CoreShop::getTools()->url(["lang" => $this->language, "act" => "list"], "coreshop_cart_list"));
    }
}
