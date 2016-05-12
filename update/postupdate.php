<?php

$db = \Pimcore\Db::get();

$db->query("
CREATE TABLE `coreshop_messaging_contact` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NULL,
  `description` varchar(1000) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `coreshop_messaging_thread_state` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `color` varchar(255) NULL,
  `finished` TINYINT(1) NOT NULL;
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `coreshop_messaging_thread` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `userId` int NULL,
  `orderId` int NULL,
  `statusId` int NULL,
  `token` varchar(255) NOT NULL,
  `contactId` int NOT NULL,
  `language` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `coreshop_messaging_message` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `threadId` int NOT NULL,
  `adminUserId` int(11) unsigned NULL,
  `message` text NOT NULL,
  `read` tinyint(1)  NOT NULL DEFAULT '1',
  FOREIGN KEY (`adminUserId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->query("INSERT INTO `users_permission_definitions` (`key`)
VALUES
  ('coreshop_permission_messaging_contact'),
  ('coreshop_permission_messaging_thread_state');");

$install = new \CoreShop\Plugin\Install();
$install->installMessagingMails();