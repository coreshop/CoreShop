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

namespace CoreShop\Controller\Action;

use CoreShop\Controller\Action;
use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Order;
use CoreShop\Model\Product;
use Pimcore\Model\Document;
use CoreShop\Model\Plugin\Payment as CorePayment;

/**
 * Class Payment
 * @package CoreShop\Controller\Action
 */
class Payment extends Action
{
    /**
     * Determines if the action is triggered by an internal-forward
     *
     * @var bool
     */
    public static $isActionForward = false;

    /**
     * Payment Module.
     *
     * @var CorePayment
     */
    protected $module;

    /**
     * @var bool
     */
    protected $opc = false;

    /**
     * @var string
     */
    protected $checkoutController = 'checkout';


    /**
     * Init Controller.
     */
    public function init()
    {
        parent::init();

        $this->view->module = $this->getModule();

        $pathsToAdd = [
            CORESHOP_TEMPLATE_BASE.'/scripts/'.strtolower($this->getModule()->getIdentifier()),
            CORESHOP_TEMPLATE_PATH.'/scripts/'.strtolower($this->getModule()->getIdentifier()),
            PIMCORE_WEBSITE_PATH.'/views/scripts/'.strtolower($this->getModule()->getIdentifier())
        ];

        /**
         * @fixme
         * Because $isActionForward is false by default,
         * the (commented out) script path order won't work if user tries to override payment scripts in website module.

         * @deprecated $isActionForward (?)
         */
        $this->view->setScriptPath(
            array_merge(
                $this->view->getScriptPaths(),
                $pathsToAdd
            )
        );

        /*

        if(self::$isActionForward) {
            $this->view->setScriptPath(
                array_merge(
                    $this->view->getScriptPaths(),
                    $pathsToAdd
                )
            );
        }
        else {
            $this->view->setScriptPath(
                array_merge(
                    $pathsToAdd,
                    $this->view->getScriptPaths()
                )
            );
        }

        */

        if ($this->getParam('opc', false)) {
            $this->opc = true;
            $this->checkoutController = 'checkout-opc';

            $this->disableLayout();
        }
    }

    /**
     * get Payment module.
     *
     * @return CorePayment
     */
    public function getModule()
    {
        if (is_null($this->module)) {
            $this->module = \CoreShop::getPaymentProvider($this->getRequest()->getModuleName());
        }

        return $this->module;
    }

    /**
     * Creates Order
     *
     * @param $language
     * @return Order
     */
    protected function createOrder($language)
    {
        $order = $this->cart->createOrder(
            $this->getModule(),
            $language
        );

        //remove user session => not valid anymore!
        \CoreShop::getTools()->deleteUserSession();

        return $order;
    }

    /**
     * Refill Cart.
     */
    public function refillCart()
    {
        //if a cart already exists, do nothing
        $session = \CoreShop::getTools()->getSession();
        if (isset($session->cartId) && !is_null($session->cartId)) {
            return false;
        }

        $user = \CoreShop::getTools()->getUser();
        if (!$user instanceof \CoreShop\Model\User) {
            return false;
        }

        $latestCart = $user->getLatestCart();
        if (!$latestCart instanceof \CoreShop\Model\Cart) {
            return false;
        }

        $newCart = \CoreShop::getTools()->getCartManager()->createCart(
            'default',
            \CoreShop::getTools()->getUser(),
            \CoreShop\Model\Shop::getShop(),
            null,
            true
        );

        \CoreShop\Tool\Service::copyObject($latestCart, $newCart);

        $items = $latestCart->getItems();
        $newItems = [];

        foreach ($items as $item) {
            $newItem = \CoreShop\Model\Cart\Item::create();
            \CoreShop\Tool\Service::copyObject($item, $newItem);
            $newItem->setKey(uniqid());
            $newItem->setParent($newCart);
            $newItem->setPublished(true);
            $newItem->save();
            $newItems[] = $newItem;

            if ($newItem->getProduct() instanceof Product) {
                $newItem->getProduct()->clearPriceCache();
            }
        }

        $newCart->setItems($newItems);
        $newCart->setOrder(null);
        $newCart->save();

        PriceRule::autoAddToCart($newCart);

        $session->cartId = $newCart->getId();
    }

    /**
     * @param \CoreShop\Model\Order $order
     * @param null $transactionId
     *
     * @return \CoreShop\Model\Order\Payment
     */
    protected function getOrderPayment($order, $transactionId = null)
    {
        $payment = $order->getOrderPaymentByIdentifier($transactionId);

        if ($payment instanceof \CoreShop\Model\Order\Payment) {
            return $payment;
        } else {
            $payment = $order->createPayment($this->getModule(), $order->getTotal(), true, $transactionId);
        }
        return $payment;
    }

    /**
     * Payment Action.
     */
    public function paymentAction()
    {
        //Overwrite this Method in your Payment-Controller and call the parent to set the order
        $this->order = $this->createOrder($this->language);
    }

    /**
     * Validate Action.
     */
    public function validateAction()
    {
        if (!$this->opc) {
            $this->coreShopForward('validate', $this->checkoutController, 'CoreShop', ['paymentViewScript' => $this->getViewScript()]);
        }
    }

    /**
     * Confirmation Action.
     */
    public function confirmationAction()
    {
        $this->view->order = $this->session->order;

        $forwardParams = [
            'module' => $this->getModule()
        ];

        if ($this->view->order instanceof Order) {
            $forwardParams['order'] = $this->view->order;
            $forwardParams['paymentViewScript'] = $this->getViewScript();

            $this->coreShopForward('confirmation', $this->checkoutController, 'CoreShop', $forwardParams);
        } else {
            $this->coreShopForward('error', $this->checkoutController, 'CoreShop', $forwardParams);
        }
    }

    /**
     * Error Action.
     */
    public function errorAction()
    {
        $forwardParams = [
            'module' => $this->getModule(),
            'error' => $this->getParam('error')
        ];


        $this->coreShopForward('error', $this->checkoutController, 'CoreShop', $forwardParams);
    }
}
