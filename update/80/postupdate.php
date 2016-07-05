<?php

\CoreShop\Model\Configuration::set("SYSTEM.BASE.TAX.ENABLED", true);

//Update all Classes, cause of new "preGetData" in CoreShop Field-Tags
$install = new \CoreShop\Plugin\Install();
$install->createFieldCollection('CoreShopUserAddress');
$install->createFieldCollection('CoreShopOrderTax');

// create object classes
$categoryClass = $install->createClass('CoreShopCategory');
$productClass = $install->createClass('CoreShopProduct');
$cartClass = $install->createClass('CoreShopCart');
$cartItemClass = $install->createClass('CoreShopCartItem');
$userClass = $install->createClass('CoreShopUser');

$orderItemClass = $install->createClass('CoreShopOrderItem');
$paymentClass = $install->createClass('CoreShopPayment');
$orderClass = $install->createClass('CoreShopOrder');
