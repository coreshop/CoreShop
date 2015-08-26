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

use Pimcore\Tool as PimTool;

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
            $priceRule->setHighlight(0);
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

            $conditions = $data['conditions'];
            $actions = $data['actions'];
            $actionInstances = array();
            $conditionInstances = array();

            $actionNamespace = "CoreShop\\Model\\PriceRule\\Action\\";
            $conditionNamespace = "CoreShop\\Model\\PriceRule\\Condition\\";

            foreach($conditions as $condition) {
                $class = $conditionNamespace . ucfirst($condition['type']);

                if(PimTool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($condition);

                    $conditionInstances[] = $instance;
                }
                else {
                    throw new \Exception(sprintf("Condition with type %s not found"), $condition['type']);
                }
            }

            foreach($actions as $action) {
                $class = $actionNamespace . ucfirst($action['type']);

                if(PimTool::classExists($class)) {
                    $instance = new $class();
                    $instance->setValues($action);

                    print_r($action);

                    $actionInstances[] = $instance;
                }
                else {
                    throw new \Exception(sprintf("Action with type %s not found"), $action['type']);
                }
            }

            $priceRule->setValues($data['settings']);
            $priceRule->setActions($actionInstances);
            $priceRule->setConditions($conditionInstances);
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