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
use CoreShop\Model\Country;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_CountryController extends Admin
{
    public function init() {

        parent::init();

        // check permissions
        $notRestrictedActions = array();
        if (!in_array($this->getParam("action"), $notRestrictedActions)) {
            $this->checkPermission("coreshop_country");
        }
    }

    public function getCountriesAction()
    {
        $list = new Country\Listing();
        $list->setOrder("ASC");
        $list->load();

        $countries = array();
        if(is_array($list->getCountries())){
            foreach ($list->getCountries() as $country) {
                $countries[] = $this->getTreeNodeConfig($country);
            }
        }
        $this->_helper->json($countries);
    }

    protected function getTreeNodeConfig($country) {
        $tmpCountry= array(
            "id" => $country->getId(),
            "text" => $country->getName(),
            "elementType" => "country",
            "qtipCfg" => array(
                "title" => "ID: " . $country->getId()
            ),
            "name" => $country->getName()
        );

        $tmpCountry["leaf"] = true;
        $tmpCountry["iconCls"] = "coreshop_icon_country";
        $tmpCountry["allowChildren"] = false;

        return $tmpCountry;
    }

    public function getCountryAction() {
        $id = $this->getParam("id");
        $country = Country::getById($id);

        if($country instanceof Country)
            $this->_helper->json(array("success" => true, "country" => $country));
        else
            $this->_helper->json(array("success" => false));
    }

    public function saveAction() {
        $id = $this->getParam("id");
        $data = $this->getParam("data");
        $country = Country::getById($id);


        if($data && $country instanceof Country) {
            $data = \Zend_Json::decode($this->getParam("data"));

            $country->setValues($data);
            $country->save();

            $this->_helper->json(array("success" => true, "country" => $country));
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
            $country = new Country();
            $country->setName($name);
            $country->setActive(1);
            $country->save();

            $this->_helper->json(array("success" => true, "country" => $country));
        }
    }

    public function removeAction() {
        $id = $this->getParam("id");
        $country = Country::getById($id);

        if($country instanceof Country) {
            $country->delete();

            $this->_helper->json(array("success" => true));
        }

        $this->_helper->json(array("success" => false));
    }
}