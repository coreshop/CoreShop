<?php

$db = \Pimcore\Db::get();
$languages = \Pimcore\Tool::getValidLanguages();
$install = new \CoreShop\Plugin\Install();

$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_order_states'");

$db->query("DROP TABLE IF EXISTS `coreshop_orderstates`;");
$db->query("DROP TABLE IF EXISTS `coreshop_orderstates_data`;");

foreach ($languages as $lang) {
    $db->query("DROP VIEW IF EXISTS `coreshop_orderstates_data_localized_".$lang."`;");
    $db->query("DROP TABLE IF EXISTS `coreshop_orderstates_query_".$lang."`;");
}

\CoreShop\Model\Configuration::set('SYSTEM.ORDER.PREFIX', 'O');
\CoreShop\Model\Configuration::set('SYSTEM.ORDER.SUFFIX', '');

foreach ($languages as $lang) {
    \CoreShop\Model\Configuration::remove("SYSTEM.MESSAGING.MAIL.CUSTOMER." . strtoupper($lang));
    \CoreShop\Model\Configuration::remove("SYSTEM.MESSAGING.MAIL.CONTACT." . strtoupper($lang));
    \CoreShop\Model\Configuration::remove("SYSTEM.MESSAGING.MAIL.CUSTOMER.RE." . strtoupper($lang));
    \CoreShop\Model\Configuration::remove("SYSTEM.MAIL.ORDER.STATES.CONFIRMATION." . strtoupper($lang));
    \CoreShop\Model\Configuration::remove("SYSTEM.MAIL.ORDER.STATES.UPDATE." . strtoupper($lang));
}

\CoreShop\Model\Configuration::remove("SYSTEM.MAIL.ORDER.BCC");
\CoreShop\Model\Configuration::remove("SYSTEM.MAIL.ORDER.NOTIFICATION");

$db->query("
    DROP TABLE IF EXISTS `coreshop_mail_rules`;
    CREATE TABLE `coreshop_mail_rules` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `sort` int NOT NULL DEFAULT 1,
      `name` varchar(50) DEFAULT NULL,
      `mailType` varchar(50) DEFAULT NULL,
      `description` text,
      `conditions` text,
      `actions` text,
      PRIMARY KEY (`id`)
    ) DEFAULT CHARSET=utf8mb4;
");

//now install new mail rules!
$install->installMailRules();
