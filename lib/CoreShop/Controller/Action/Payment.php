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

namespace CoreShop\Controller\Action;

use CoreShop\Controller\Action;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Order;
use CoreShop\Model\OrderState;
use CoreShop\Plugin;

use Pimcore\Model\Document;
use Pimcore\Model\Object\CoreShopPayment;
use Pimcore\Model\Object\CoreShopOrder;
use Pimcore\Model\Object\CoreShopUser;
use CoreShop\Model\Plugin\Payment as CorePayment;

class Payment extends Action {

    /**+
     * @var CorePayment
     */
    protected $module;

    public function init() {
        parent::init();

        $this->view->document = $this->document = Document::getByPath("/" . $this->language . "/shop");
        $this->view->module = $this->getModule();

        $this->view->setScriptPath(
            array_merge(
                $this->view->getScriptPaths(),
                array(
                    CORESHOP_TEMPLATE_PATH . '/views/scripts/' . $this->getModule()->getIdentifier()
                )
            )
        );
    }

    /**
     * @return CorePayment
     */
    public function getModule()
    {
        if(is_null($this->module)) {
            $this->module = Plugin::getPaymentProvider($this->getParam("module"));
        }

        return $this->module;
    }

    public function paymentAction()
    {
        throw new UnsupportedException("This Method has to implemented by the Plugin Controller");
    }

    public function validateAction() {
        $this->test = 1;
    }

    public function confirmationAction() {
        $this->prepareCart();
        $this->cart->delete();

        if(!$this->session->order instanceof Order)
            $this->redirect("/" . $this->view->language);

        $this->view->order = $this->session->order;

        unset($this->session->order);
        unset($this->session->cart);
        unset($this->session->cartId);
    }
}
