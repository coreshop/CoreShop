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
use CoreShop\Model\Zone;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_ZoneController extends Admin
{
    public function init() {

        parent::init();

        // check permissions
        $notRestrictedActions = array();
        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_zone");
        }
    }

    public function getAction()
    {
        $list = new Zone\Listing();
        $list->setOrder("ASC");
        $list->load();

        $this->_helper->json($list->getData());
    }

    public function getZonesAction()
    {
        $list = new Zone\Listing();
        $list->setOrder("ASC");
        $list->load();

        $zones = array();
        if(is_array($list->getData())){
            foreach ($list->getData() as $zone) {
                $zones[] = $this->getTreeNodeConfig($zone);
            }
        }
        $this->_helper->json($zones);
    }

    protected function getTreeNodeConfig($zone) {
        $tmpZone= array(
            "id" => $zone->getId(),
            "text" => $zone->getName(),
            "elementType" => "zone",
            "qtipCfg" => array(
                "title" => "ID: " . $zone->getId()
            ),
            "name" => $zone->getName()
        );

        $tmpZone["leaf"] = true;
        $tmpZone["iconCls"] = "coreshop_icon_zone";
        $tmpZone["allowChildren"] = false;

        return $tmpZone;
    }

    public function getZoneAction() {
        $id = $this->getParam("id");
        $zone = Zone::getById($id);

        if($zone instanceof Zone)
            $this->_helper->json(array("success" => true, "zone" => $zone));
        else
            $this->_helper->json(array("success" => false));
    }

    public function saveAction() {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $zone = Zone::getById($id);


        if($data && $zone instanceof Zone) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $zone->setValues($data);
            $zone->save();

            $this->_helper->json(array("success" => true, "zone" => $zone));
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
            $zone = new Zone();
            $zone->setName($name);
            $zone->setActive(1);
            $zone->save();

            $this->_helper->json(array("success" => true, "zone" => $zone));
        }
    }

    public function removeAction() {
        $id = $this->getParam("id");
        $zone = Zone::getById($id);

        if($zone instanceof Zone) {
            $zone->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}