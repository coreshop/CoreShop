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

//Migrate to Multishops
$db = \Pimcore\Db::get();

$db->query("DROP TABLE IF EXISTS `coreshop_shops`;
CREATE TABLE `coreshop_shops` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `siteId` int DEFAULT 0
  `name` varchar(255) NOT NULL,
  `currencyId` int NOT NULL,
  `template` varchar(255) NOT NULL,
  `isDefault` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->query("INSERT INTO `coreshop_shops` (`name`, `currencyId`, `template`, `isDefault`)
VALUES ('Default', '1', '".\CoreShop\Model\Configuration::get("SYSTEM.TEMPLATE.NAME")."', '1');");

$modelsToUpdate = array("Country", "Carrier", "CustomerGroup", "Zone", "TaxRuleGroup", "Manufacturer", "NumberRange");

foreach($modelsToUpdate as $model) {
    $class = 'CoreShop\Model\\' . $model;

    $class = new $class();
    $tableName = $class->getDao()->getTableName();

    if($class->isMultiShop()) {
        $db->query("DROP TABLE IF EXISTS `" . $tableName . "_shops`;
        CREATE TABLE IF NOT EXISTS `" . $tableName . "_shops` (
          `oId` int(11) NOT NULL,
          `shopId` int(11) NOT NULL,
          PRIMARY KEY (`oId`,`shopId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $list = $class::getList();

        foreach($list as $object) {
            $db->query("INSERT INTO `" . $tableName . "_shops` VALUES (".$object->getId().", 1)");
        }
    }
}

$indexes = \CoreShop\Model\Index::getAll();

foreach($indexes as $index) {
    $tableName =  'coreshop_index_mysql_'. $index->getName();

    $db->query("ALTER TABLE `$tableName` ADD `shops` varchar(255) NOT NULL AFTER `active`;");
    $db->query("UPDATE $tableName SET `shops`=',".\CoreShop\Model\Shop::getDefaultShop()->getId().",'");
}

$products = \CoreShop\Model\Product::getList();

foreach($products->load() as $pro) {
    $pro->setShops([
        \CoreShop\Model\Shop::getDefaultShop()->getId()
    ]);
    $pro->save();
}

$db->query("ALTER TABLE `coreshop_numberranges`
ADD `shopId` int(11) NOT NULL AFTER `id`;");

$db->query("ALTER TABLE `coreshop_numberranges`
ADD UNIQUE `type_shopId` (`type`, `shopId`),
DROP INDEX `type`;");

$db->query("UPDATE `coreshop_numberranges` SET `shopId` = 1;");