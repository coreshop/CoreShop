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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Controller;

use CoreShop\Model\Cart;
use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\Cart\PriceRule;
use Pimcore\Tool\Session;

class Action extends \Website\Controller\Action
{
    /**
     * Cart.
     *
     * @var Cart
     */
    protected $cart;

    /**
     * Session.
     *
     * @var Session
     */
    protected $session;

    /**
     * Init CoreShop Controller.
     */
    public function init()
    {
        parent::init();

        \Pimcore::getEventManager()->trigger('coreshop.controller.init', $this);

        $this->view->setScriptPath(
            array_merge(
                $this->view->getScriptPaths(),
                array(
                    CORESHOP_TEMPLATE_PATH.'/scripts',
                    CORESHOP_TEMPLATE_PATH.'/scripts/coreshop',
                    CORESHOP_TEMPLATE_PATH.'/layouts',
                    PIMCORE_WEBSITE_PATH.'/views/scripts',
                )
            )
        );

        $this->view->addHelperPath(CORESHOP_PATH.'/lib/CoreShop/View/Helper', 'CoreShop\View\Helper');

        $this->session = $this->view->session = Tool::getSession();

        $this->view->country = Tool::getCountry();

        $this->prepareCart();

        $this->view->isShop = true;

        $this->enableLayout();
        $this->setLayout(Plugin::getLayout());
    }

    /**
     * Prepare Cart.
     *
     * If a user is available in session, set the user to the cart
     *
     * @throws \Exception
     */
    protected function prepareCart()
    {
        $this->cart = $this->view->cart = Tool::prepareCart();

        PriceRule::autoRemoveFromCart($this->cart);
        PriceRule::autoAddToCart($this->cart);
    }
}
