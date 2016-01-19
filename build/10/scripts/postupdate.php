<?php

//add catalog-mode to config
$config = \CoreShop\Config::getConfig();

$config = $config->toArray();
$config['base']['guest-checkout'] = false;

$config = new \Zend_Config($config, true);
$writer = new \Zend_Config_Writer_Xml(array(
    "config" => $config,
    "filename" => CORESHOP_CONFIGURATION
));
$writer->write();