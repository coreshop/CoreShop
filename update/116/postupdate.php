<?php

$db = \Pimcore\Db::get();
$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_order_states'");
$db->query("DROP TABLE IF EXISTS `coreshop_orderstates`;");
//remove coreshop_orderstates localized? how?

\CoreShop\Model\Configuration::set('SYSTEM.ORDER.PREFIX', 'O');
\CoreShop\Model\Configuration::set('SYSTEM.ORDER.SUFFIX', '');