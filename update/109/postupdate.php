<?php

$db = \Pimcore\Db::get();

$db->query("DROP TABLE IF EXISTS `coreshop_visitor`;
CREATE TABLE `coreshop_visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `shopId` int(11) NOT NULL,
  `userId` int NOT NULL,
  `ip` int NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `creationDate` bigint(20) NOT NULL
);

DROP TABLE IF EXISTS `coreshop_visitor_page`;
CREATE TABLE `coreshop_visitor_page` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `visitorId` int NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `creationDate` bigint(20) NOT NULL
);

DROP TABLE IF EXISTS `coreshop_visitor_source`;
CREATE TABLE `coreshop_visitor_source` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `visitorId` int NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `requestUrl` varchar(255) NOT NULL,
  `pageId` int(11) NOT NULL,
  `creationDate` bigint(20) NOT NULL
);");

\CoreShop\Model\Configuration::set("SYSTEM.VISITORS.TRACK", false);

$install = new \CoreShop\Plugin\Install();
$install->createClass('CoreShopOrder');