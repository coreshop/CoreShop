<?php

$db = \Pimcore\Db::get();

$db->query("DROP TABLE IF EXISTS `coreshop_product_pricerules`;
CREATE TABLE `coreshop_product_pricerules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `conditions` text,
  `actions` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->query("INSERT INTO `users_permission_definitions` (`key`)
VALUES
  ('coreshop_permission_productSpecificPrices'),
  ('coreshop_permission_productPriceRules');");

$db->query("ALTER TABLE `coreshop_product_specificprice`
ADD `inherit` tinyint(5) NOT NULL DEFAULT '1' AFTER `o_id`;");