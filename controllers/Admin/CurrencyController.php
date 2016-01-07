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

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\Currency;


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

    public function getAction()
    {
        $list = new Currency\Listing();
        $list->setOrder("ASC");
        $list->load();

        $this->_helper->json($list->getData());
    }

    public function getCurrenciesAction()
    {
        $list = new Currency\Listing();
        $list->setOrder("ASC");
        $list->load();

        $currencies = array();
        if(is_array($list->getData())){
            foreach ($list->getData() as $currency) {
                $currencies[] = $this->getTreeNodeConfig($currency);
            }
        }
        $this->_helper->json($currencies);
    }

    protected function getTreeNodeConfig($currency) {
        $tmpCurrency= array(
            "id" => $currency->getId(),
            "text" => $currency->getName(),
            "elementType" => "currency",
            "qtipCfg" => array(
                "title" => "ID: " . $currency->getId()
            ),
            "name" => $currency->getName()
        );

        $tmpCurrency["leaf"] = true;
        $tmpCurrency["iconCls"] = "coreshop_icon_currency";
        $tmpCurrency["allowChildren"] = false;

        return $tmpCurrency;
    }

    public function getCurrencyAction() {
        $id = $this->getParam("id");
        $currency = Currency::getById($id);

        if($currency instanceof Currency)
            $this->_helper->json(array("success" => true, "currency" => $currency));
        else
            $this->_helper->json(array("success" => false));
    }

    public function saveAction() {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $currency = Currency::getById($id);


        if($data && $currency instanceof Currency) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $currency->setValues($data);
            $currency->save();

            $this->_helper->json(array("success" => true, "currency" => $currency));
        }
        else
            $this->_helper->json(array("success" => false));
    }

    public function addAction() {
        $name = $this->getParam("name");

        if(strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        }
        else {
            $currency = new Currency();
            $currency->setName($name);
            $currency->save();

            $this->_helper->json(array("success" => true, "currency" => $currency));
        }
    }

    public function removeAction() {
        $id = $this->getParam("id");
        $currency = Currency::getById($id);

        if($currency instanceof Currency) {
            $currency->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}