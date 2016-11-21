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
 * Class CoreShop_InvoiceController
 */
class CoreShop_InvoiceController extends Action
{
    public function init()
    {
        parent::init();

        $this->disableLayout();

        $this->view->language = $this->getParam("language");
        $this->view->order = $this->getParam("order");
        $this->view->invoice = $this->getParam("invoice");
    }

    public function invoiceAction() {

    }

    public function headerAction() {

    }

    public function footerAction() {

    }
}