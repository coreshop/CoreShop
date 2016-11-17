<?php

$db = \Pimcore\Db::get();

$db->query("ALTER TABLE `coreshop_product_filters` ADD `similarities` text NOT NULL;");