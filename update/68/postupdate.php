<?php

$install = new \CoreShop\Plugin\Install();
$install->installObjectData("threadContacts", "Messaging\\");
$install->installMessagingContacts();


$db = \Pimcore\Db::get();
$db->query("ALTER TABLE `coreshop_product_filters` ADD `similarities` text COLLATE 'utf8_general_ci' NOT NULL AFTER `filters`;");