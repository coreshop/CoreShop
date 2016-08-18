<?php

$db = \Pimcore\Db::get();

$db->query("ALTER TABLE `coreshop_product_specificprice`
ADD `priority` int(11) NOT NULL DEFAULT 0 AFTER `inherit`;");

$db->query("UPDATE `coreshop_product_specificprice` SET priority=1;");