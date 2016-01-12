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

    public function getCarriersAction()
    {
        $list = new Carrier\Listing();
        $list->setOrder("ASC");
        $list->setOrderKey("name");
        $list->load();

        $carriers = array();
        if(is_array($list->getData())){
            foreach ($list->getData() as $carrier) {
                $carriers[] = $this->getTreeNodeConfig($carrier);
            }
        }

        $this->_helper->json($carriers);
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
        $ranges = $carrier->getRanges();

        if($carrier instanceof Carrier)
            $this->_helper->json(array("success" => true, "total" => count($ranges), "data" => $ranges));
        else
            $this->_helper->json(array("success" => false));
    }

    public function getPricesAction() {
        $id = $this->getParam("carrier");
        $carrier = Carrier::getById($id);

        if($carrier instanceof Carrier)
        {
            $zones = \CoreShop\Model\Zone::getAll();
            $ranges = $carrier->getRanges();
            $prices = array();

            foreach($ranges as $range)
            {
                $price = array(
                    "range" => $range->getDelimiter1() . " - " . $range->getDelimiter2(),
                    "rangeId" => $range->getId()
                );

                foreach($zones as $zone)
                {
                    $deliveryPrice = Carrier\DeliveryPrice::getForCarrierInZone($carrier, $range, $zone);

                    $price['zone_' . $zone->getId()] = $deliveryPrice instanceof Carrier\DeliveryPrice ? $deliveryPrice->getPrice() : "";
                }

                $prices[] = $price;
            }

            $this->_helper->json(array("success" => true, "count" => count($prices), "data" => $prices));
        }
        else {
            $this->_helper->json(array("success" => false));
        }
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
            $carrier->setNeedsRange(0);
            $carrier->save();

            $config = $this->getTreeNodeConfig($carrier);
            $config['success'] = true;

            $this->_helper->json($config);
        }
    }

    public function getAction() {
        $id = $this->getParam("id");
        $carrier = Carrier::getById($id);

        if($carrier instanceof Carrier) {
            $this->_helper->json($carrier);
        }
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

            $ranges = $data['range'];
            $rangesToKeep = array();
            if(is_array($ranges)) {
                foreach($ranges as $range) {
                    $rangeObject = null;
                    $deliveryPriceObject = null;

                    if($range['id']) {
                        $rangeObject = Carrier\AbstractRange::getById($range['id'], $carrier->getShippingMethod());
                    }

                    if(is_null($rangeObject)) {
                        $rangeObject = Carrier\AbstractRange::create($carrier->getShippingMethod());
                    }

                    $rangeObject->setCarrier($carrier);
                    $rangeObject->setDelimiter1($range['delimiter1']);
                    $rangeObject->setDelimiter2($range['delimiter2']);
                    $rangeObject->save();

                    $rangesToKeep[] = $rangeObject->getId();
                }
            }

            if(count($rangesToKeep) > 0)
            {
                $carrier->setNeedsRange(true);
            }
            else {
                $carrier->setNeedsRange(false);
            }

            $carrier->save();

            $deliveryPrices = $data['deliveryPrices'];

            if(is_array($deliveryPrices)) {
                $zones = \CoreShop\Model\Zone::getAll();

                foreach($deliveryPrices as $deliveryPrice)
                {
                    $range = Carrier\AbstractRange::getById($deliveryPrice['rangeId'], $carrier->getShippingMethod());

                    foreach($zones as $zone)
                    {
                        if(array_key_exists('zone_' . $zone->getId(), $deliveryPrice)) {
                            $price = $deliveryPrice['zone_' . $zone->getId()];

                            $deliveryPriceObject = Carrier\DeliveryPrice::getForCarrierInZone($carrier, $range, $zone);

                            if (is_null($deliveryPriceObject)) {
                                $deliveryPriceObject = new Carrier\DeliveryPrice();

                                $deliveryPriceObject->setZone($zone);
                                $deliveryPriceObject->setCarrier($carrier);
                                $deliveryPriceObject->setRange($range);
                                $deliveryPriceObject->setRangeType($carrier->getShippingMethod());
                            }

                            $deliveryPriceObject->setPrice($price);

                            if(is_numeric($price)) {
                                $deliveryPriceObject->save();
                            }
                            else {
                                $deliveryPriceObject->delete();
                            }
                        }
                    }
                }
            }

            $ranges = $carrier->getRanges();

            foreach($ranges as $range) {
                if(!in_array($range->getId(), $rangesToKeep)) {
                    $range->delete();
                }
            }

            $this->_helper->json(array("success" => true, "carrier" => $carrier));
        } else
            $this->_helper->json(array("success" => false));
    }

    public function deleteAction() {
        $id = $this->getParam("id");
        $carrier = Carrier::getById($id);

        if($carrier instanceof Carrier) {
            $carrier->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}