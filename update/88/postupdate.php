<?php

$db = \Pimcore\Db::get();

$db->query("DROP TABLE coreshop_carriers_delivery_price");
$db->query("DROP TABLE coreshop_carriers_range_price");
$db->query("DROP TABLE coreshop_carriers_range_weight");