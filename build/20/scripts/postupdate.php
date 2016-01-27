<?php

use \CoreShop\Model\Configuration;

if (!defined("CORESHOP_CONFIGURATION")) define("CORESHOP_CONFIGURATION", CORESHOP_CONFIGURATION_PATH . "/coreshop-config.xml");

$config = new \Zend_Config_Xml(CORESHOP_CONFIGURATION);
$config = $config->toArray();

Configuration::set("SYSTEM.BASE.CURRENCY", is_int($config['base']['base-currency']) ? $config['base']['base-currency'] : null);
Configuration::set("SYSTEM.BASE.CATALOGMODE", is_bool($config['base']['catalog-mode']) ? $config['base']['catalog-mode'] : false);
Configuration::set("SYSTEM.BASE.GUESTCHECKOUT", is_bool($config['base']['guest-checkout']) ? $config['base']['guest-checkout'] : false);
Configuration::set("SYSTEM.PRODUCT.DEFAULTIMAGE", is_int($config['product']['default-image']) ? $config['product']['default-image'] : null);
Configuration::set("SYSTEM.PRODUCT.DAYSASNEW", is_int($config['product']['days-as-new']) ? $config['product']['days-as-new'] : 0);
Configuration::set("SYSTEM.CATEGORY.DEFAULTIMAGE", is_int($config['category']['default-image']) ? $config['category']['default-image'] : null);
Configuration::set("SYSTEM.TEMPLATE.NAME", $config['template']['name']);
Configuration::set("SYSTEM.INVOICE.CREATE", boolval($config['invoice']['create']));
Configuration::set("SYSTEM.INVOICE.PREFIX", $config['invoice']['prefix']);
Configuration::set("SYSTEM.INVOICE.SUFFIX", $config['invoice']['suffix']);
Configuration::set("SYSTEM.ORDERSTATE.QUEUE", intval($config['orderstate']['queue']));
Configuration::set("SYSTEM.ORDERSTATE.PAYMENT", intval($config['orderstate']['payment']));
Configuration::set("SYSTEM.ORDERSTATE.PREPERATION", intval($config['orderstate']['preperation']));
Configuration::set("SYSTEM.ORDERSTATE.SHIPPING", intval($config['orderstate']['shipping']));
Configuration::set("SYSTEM.ORDERSTATE.DELIVERED", intval($config['orderstate']['delivered']));
Configuration::set("SYSTEM.ORDERSTATE.CANCELED", intval($config['orderstate']['canceled']));
Configuration::set("SYSTEM.ORDERSTATE.REFUND", intval($config['orderstate']['refund']));
Configuration::set("SYSTEM.ORDERSTATE.ERROR", intval($config['orderstate']['error']));
Configuration::set("SYSTEM.ORDERSTATE.OUTOFSTOCK", intval($config['orderstate']['outofstock']));
Configuration::set("SYSTEM.ORDERSTATE.BANKWIRE", intval($config['orderstate']['bankwire']));
Configuration::set("SYSTEM.ORDERSTATE.OUTOFSTOCK_UNPAID", intval($config['orderstate']['outofstock_unpaid']));
Configuration::set("SYSTEM.ORDERSTATE.COD", intval($config['orderstate']['cod']));
Configuration::set("SYSTEM.ISINSTALLED", boolval($config['isInstalled']));