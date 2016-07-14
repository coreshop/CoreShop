<?php

$db = \Pimcore\Db::get();

$db->query("DROP TABLE IF EXISTS `coreshop_shops`;
CREATE TABLE `coreshop_shops` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `siteId` int DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL,
  `isDefault` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->query("INSERT INTO `coreshop_shops` (`name`, `template`, `isDefault`)
VALUES ('Default', '".\CoreShop\Model\Configuration::get("SYSTEM.TEMPLATE.NAME")."', '1');");
