<?php

$db = \Pimcore\Db::get();

$db->query("CREATE TABLE `coreshop_voucher_codes` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` varchar(255) NOT NULL,
  `creationDate` int NOT NULL,
  `used` tinyint(1) NOT NULL,
  `uses` tinyint(12) NOT NULL,
  `priceRuleId` int NOT NULL
);");

$db->query("ALTER TABLE coreshop_pricerules RENAME coreshop_cart_pricerules;");

$db->query("ALTER TABLE `coreshop_cart_pricerules` ADD `usagePerVoucherCode` INT NOT NULL DEFAULT '0' AFTER `highlight`, ADD `useMultipleVoucherCodes` tinyint(1) NOT NULL DEFAULT '0' AFTER `usagePerVoucherCode`;");