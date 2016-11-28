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

/**
 * Class CoreShop_OrderPrintController
 */
class CoreShop_OrderPrintController extends \CoreShop\Controller\Action
{
    public function init()
    {
        parent::init();

        $this->disableLayout();

        $this->view->language = $this->getParam("language");
        $this->view->order = $this->getParam("order");
        $this->view->type = $this->getParam("type");
    }

    public function headerAction() {

    }

    public function footerAction() {

    }

    public function invoiceAction() {
        $this->view->invoice = $this->getParam("invoice");
    }

    public function shipmentAction() {
        $this->view->shipment = $this->getParam("shipment");
    }
}