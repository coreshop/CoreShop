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
use CoreShop\Model\Carrier;

use Pimcore\Controller\Action\Admin;

use Pimcore\Tool as PimTool;

class CoreShop_Admin_CarrierController extends Admin
{
    public function listAction()
    {
        $list = new Carrier\Listing();

        $data = array();
        if (is_array($list->getData())) {
            foreach ($list->getData() as $carrier) {
                $data[] = $this->getTreeNodeConfig($carrier);
            }
        }
        $this->_helper->json($data);
    }

    protected function getTreeNodeConfig($carrier)
    {
        $tmpCarrier = array(
            "id" => $carrier->getId(),
            "text" => $carrier->getName(),
            "elementType" => "carrier",
            "qtipCfg" => array(
                "title" => "ID: " . $carrier->getId()
            ),
            "name" => $carrier->getName()
        );

        $tmpCarrier["leaf"] = true;
        $tmpCarrier["iconCls"] = "coreshop_icon_carrier";
        $tmpCarrier["allowChildren"] = false;

        return $tmpCarrier;
    }

    public function getRangeAction() {
        $id = $this->getParam("carrier");
        $carrier = Carrier::getById($id);

        if($carrier instanceof Carrier)
            $this->_helper->json(array("success" => true, "total" => count($carrier->getRange()), "data" => $carrier->getRange()));
        else
            $this->_helper->json(array("success" => false));
    }

    public function addAction() {
        $name = $this->getParam("name");

        if(strlen($name) <= 0) {
            $this->helper->json(array("success" => false, "message" => $this->getTranslator()->translate("Name must be set")));
        }
        else {
            $carrier = new Carrier();
            $carrier->setName($name);
            $carrier->setLabel($name);
            $carrier->setGrade(1);
            $carrier->setShippingMethod("weight");
            $carrier->setRangeBehaviour("largest");
            $carrier->setMaxDepth(0);
            $carrier->setMaxHeight(0);
            $carrier->setMaxWeight(0);
            $carrier->setMaxWidth(0);
            $carrier->save();

            $config = $this->getTreeNodeConfig($carrier);
            $config['success'] = true;

            $this->_helper->json($config);
        }
    }

    public function getAction() {
        $id = $this->getParam("id");
        $carrier = Carrier::getById($id);

        if($carrier instanceof Carrier)
            $this->_helper->json($carrier);
        else
            $this->_helper->json(array("success" => false));
    }

    public function saveAction()
    {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $carrier = Carrier::getById($id);

        if ($data && $carrier instanceof Carrier) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $carrier->setValues($data['settings']);
            $carrier->save();

            $ranges = $data['range'];

            if(is_array($ranges)) {
                foreach($ranges as $range) {

                    $rangeObject = null;
                    $deliveryPriceObject = null;

                    if($range['id']) {
                        $rangeObject = Carrier\RangeWeight::getById($range['id']);
                    }

                    if(is_null($rangeObject)) {
                        $rangeObject = new Carrier\RangeWeight();
                    }

                    $rangeObject->setCarrier($id);
                    $rangeObject->setDelimiter1($range['delimiter1']);
                    $rangeObject->setDelimiter2($range['delimiter2']);
                    $rangeObject->save();

                    $deliveryPriceObject = Carrier\DeliveryPrice::getByCarrierAndRange($id, $rangeObject->getId());

                    if(is_null($deliveryPriceObject)) {
                        $deliveryPriceObject = new Carrier\DeliveryPrice();
                    }

                    $deliveryPriceObject->setRange($rangeObject->getId());
                    $deliveryPriceObject->setPrice($range['price']);
                    $deliveryPriceObject->setCarrier($id);
                    $deliveryPriceObject->setRangeType($carrier->getShippingMethod());
                    $deliveryPriceObject->save();
                }
            }

            $this->_helper->json(array("success" => true, "carrier" => $carrier));
        } else
            $this->_helper->json(array("success" => false));
    }

    public function deleteAction() {
        $id = $this->getParam("id");
        $priceRule = Carrier::getById($id);

        if($carrier instanceof Carrier) {
            $carrier->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}