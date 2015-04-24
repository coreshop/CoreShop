<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\Currency;

use Pimcore\Model\Object\CoreShopCurrency;
use Pimcore\Model\Object\CoreShopCountry;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_CurrencyController extends Admin
{
    public function init() {

        parent::init();

        // check permissions
        $notRestrictedActions = array();
        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_currency");
        }
    }

    public function listAction ()
    {
        $list = new Currency\Listing();
        $list->setLimit($this->getParam("limit"));
        $list->setOffset($this->getParam("start"));

        if($this->getParam("sort")) {
            $list->setOrderKey($this->getParam("sort"));
            $list->setOrder($this->getParam("dir"));
        }

        $items = $list->load();
        $this->_helper->json(array("data" => $items, "success" => true, "total" => $list->getTotalCount()));
    }
}