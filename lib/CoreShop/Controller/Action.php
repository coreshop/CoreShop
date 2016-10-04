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

namespace CoreShop\Controller;

use CoreShop\Exception;
use CoreShop\Model\Cart;
use CoreShop\Model\Shop;
use CoreShop\Plugin;
use CoreShop\Model\Cart\PriceRule;
use Pimcore\Tool\Session;

/**
 * Class Action
 * @package CoreShop\Controller
 */
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
        //Needs to be done within the controller, otherwise the Site is unkown
        $this->initTemplate();

        \Pimcore::getEventManager()->trigger('coreshop.controller.init', $this);

        $this->view->setScriptPath(
            array_merge(
                $this->view->getScriptPaths(),
                array(
                    CORESHOP_TEMPLATE_BASE.'/scripts',
                    CORESHOP_TEMPLATE_BASE.'/scripts/coreshop',
                    CORESHOP_TEMPLATE_BASE.'/layouts',
                    CORESHOP_TEMPLATE_PATH.'/scripts',
                    CORESHOP_TEMPLATE_PATH.'/scripts/coreshop',
                    CORESHOP_TEMPLATE_PATH.'/layouts',
                    PIMCORE_WEBSITE_PATH.'/views/scripts/coreshop',
                )
            )
        );

        $this->view->addHelperPath(CORESHOP_PATH.'/lib/CoreShop/View/Helper', 'CoreShop\View\Helper');

        $this->session = $this->view->session = \CoreShop::getTools()->getSession();

        $this->view->country = \CoreShop::getTools()->getCountry();

        $this->prepareCart();

        $this->view->isShop = true;

        $this->enableLayout();
        $this->setLayout(Plugin::getLayout());
    }

    /**
     * Init the Template for Shop
     */
    protected function initTemplate()
    {
        //Throws Exception when Multishop is wrong configured
        $shop = Shop::getShop();

        \CoreShop::getTools()->initTemplateForShop($shop);
    }

    /**
     * Prepare Cart.
     *
     * If a user is available in session, set the user to the cart
     *
     * @throws Exception
     */
    protected function prepareCart()
    {
        $this->cart = $this->view->cart = \CoreShop::getTools()->prepareCart();

        if ($this->cart->getId()) {
            PriceRule::autoRemoveFromCart($this->cart);
            PriceRule::autoAddToCart($this->cart);
        }
    }

    /**
     * @param $action
     * @param null $controller
     * @param null $module
     * @param array|null $params
     */
    public function coreShopForward($action, $controller = null, $module = null, array $params = null)
    {
        $this->forward($action, $controller, $module, $params);

        $request = clone $this->getRequest();

        if ($request->getModuleName() === 'CoreShop') {
            $frontController = \Zend_Controller_Front::getInstance();

            $request->setModuleName(PIMCORE_FRONTEND_MODULE);

            if ($frontController->getDispatcher()->isDispatchable($request)) {
                $this->getRequest()->setModuleName(PIMCORE_FRONTEND_MODULE);
            }
        }
    }
}
