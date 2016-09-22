<?php

$db = \Pimcore\Db::get();

$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_productSpecificPrices'");
$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_customer_groups'");

$db->query("UPDATE users_permission_definitions SET `key`='coreshop_permission_product_price_rules' WHERE `key`='coreshop_permission_productPriceRules'");
$db->query("UPDATE users_permission_definitions SET `key`='coreshop_permission_price_rules' WHERE `key`='coreshop_permission_priceRules'");
$db->query("UPDATE users_permission_definitions SET `key`='coreshop_permission_order_states' WHERE `key`='coreshop_permission_orderStates'");