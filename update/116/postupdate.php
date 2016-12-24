<?php

$db = \Pimcore\Db::get();
$languages = \Pimcore\Tool::getValidLanguages();

$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_order_states'");

$db->query("DROP TABLE IF EXISTS `coreshop_orderstates`;");
$db->query("DROP TABLE IF EXISTS `coreshop_orderstates_data`;");

foreach ($languages as $lang) {
    $db->query("DROP VIEW IF EXISTS `coreshop_orderstates_data_localized_".$lang."`;");
    $db->query("DROP TABLE IF EXISTS `coreshop_orderstates_query_".$lang."`;");
}

\CoreShop\Model\Configuration::set('SYSTEM.ORDER.PREFIX', 'O');
\CoreShop\Model\Configuration::set('SYSTEM.ORDER.SUFFIX', '');
