<?php

use CoreShop\Plugin;
use CoreShop\Config;

use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_SettingsController extends Admin
{
    public function getAction()
    {
        $values = Config::getConfig();

        $valueArray = $values->toArray();

        $response = array(
            "values" => $valueArray,
        );

        $this->_helper->json($response);
        $this->_helper->json(false);
    }

    public function setAction()
    {
        $values = \Zend_Json::decode($this->getParam("data"));

        // convert all special characters to their entities so the xml writer can put it into the file
        $values = array_htmlspecialchars($values);

        // email settings
        $oldConfig = Config::getConfig();
        $oldValues = $oldConfig->toArray();

        $settings = array(
            "product" => array(
                "default-image" => $values["product.default-image"]
            ),
            "category" => array(
                "default-image" => $values["category.default-image"]
            )
        );

        $config = new \Zend_Config($settings, true);
        $writer = new \Zend_Config_Writer_Xml(array(
            "config" => $config,
            "filename" => CORESHOP_CONFIGURATION
        ));
        $writer->write();

        $this->_helper->json(array("success" => true));

    }
}
