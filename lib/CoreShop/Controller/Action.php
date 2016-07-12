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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Controller;

use CoreShop\Exception;
use CoreShop\Model\Cart;
use CoreShop\Model\Shop;
use CoreShop\Plugin;
use CoreShop\Tool;
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
        $this->initTemplate();

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
     * Init the Template for Shop
     */
    protected function initTemplate() {
        //Throws Exception when Multishop is wrong configured
        $shop = Shop::getShop();
        $template = $shop->getTemplate();

        if (!$template) {
            die("No template configured");
        }

        $templateBasePath = '';
        $templateResources = '';

        if (is_dir(PIMCORE_WEBSITE_PATH . '/views/scripts/coreshop/template/' . $template)) {
            $templateBasePath = PIMCORE_WEBSITE_PATH . "/views/scripts/coreshop/template";
            $templateResources = "/website/views/scripts/coreshop/template/" . $template . "/static/";
        }

        if (!defined("CORESHOP_TEMPLATE_BASE_PATH")) {
            define("CORESHOP_TEMPLATE_BASE_PATH", $templateBasePath);
        }
        if (!defined("CORESHOP_TEMPLATE_NAME")) {
            define("CORESHOP_TEMPLATE_NAME", $template);
        }
        if (!defined("CORESHOP_TEMPLATE_PATH")) {
            define("CORESHOP_TEMPLATE_PATH", CORESHOP_TEMPLATE_BASE_PATH . "/" . $template);
        }
        if (!defined("CORESHOP_TEMPLATE_RESOURCES")) {
            define("CORESHOP_TEMPLATE_RESOURCES", $templateResources);
        }

        if (!is_dir(CORESHOP_TEMPLATE_PATH)) {
            \Logger::critical(sprintf("Template with name '%s' not found. (%s)", $template, CORESHOP_TEMPLATE_PATH));
        }
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
        $this->cart = $this->view->cart = Tool::prepareCart();

        PriceRule::autoRemoveFromCart($this->cart);
        PriceRule::autoAddToCart($this->cart);
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

            $request->setModuleName('Default');

            if ($frontController->getDispatcher()->isDispatchable($request)) {
                $this->getRequest()->setModuleName('Default');
            }
        }
    }
}
