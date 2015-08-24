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
use CoreShop\Model\PriceRule;


use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_PricerulesController extends Admin
{
    public function listAction() {
        $list = new PriceRule\Listing();

        $data = array();
        if(is_array($list->getPriceRules())){
            foreach ($list->getPriceRules() as $pricerule) {
                $data[] = $this->getTreeNodeConfig($pricerule);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig($priceRule) {
        $tmpPriceRule= array(
            "id" => $priceRule->getId(),
            "text" => $priceRule->getName(),
            "elementType" => "pricerule",
            "qtipCfg" => array(
                "title" => "ID: " . $priceRule->getId()
            ),
            "name" => $priceRule->getName()
        );

        $tmpPriceRule["leaf"] = true;
        $tmpPriceRule["iconCls"] = "coreshop_icon_price_rule";
        $tmpPriceRule["allowChildren"] = false;

        return $tmpPriceRule;
    }

    public function getConfigAction() {
        $this->_helper->json(array(
            "success" => true,
            "conditions" => PriceRule::$availableConditions,
            "actions" => PriceRule::$availableActions
        ));
    }

    public function addAction() {
        $name = $this->getParam("name");

        if(strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        }
        else {
            $priceRule = new PriceRule();
            $priceRule->setName($name);
            $priceRule->setActive(0);
            $priceRule->save();

            $this->_helper->json(array("success" => true, "priceRule" => $priceRule));
        }
    }

    public function getAction() {
        $id = $this->getParam("id");
        $priceRule = PriceRule::getById($id);

        if($priceRule instanceof PriceRule)
            $this->_helper->json($priceRule);
        else
            $this->_helper->json(array("success" => false));
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $priceRule = PriceRule::getById($id);


        if ($data && $priceRule instanceof PriceRule) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $priceRule->setValues($data);
            $priceRule->save();

            $this->_helper->json(array("success" => true, "priceRule" => $priceRule));
        } else
            $this->_helper->json(array("success" => false));
    }

    public function removeAction() {
        $id = $this->getParam("id");
        $priceRule = PriceRule::getById($id);

        if($priceRule instanceof PriceRule) {
            $priceRule->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}