<?php

$db = \Pimcore\Db::get();

$data = $db->fetchAll("SHOW COLUMNS FROM coreshop_product_filters");
$columns = [];

foreach ($data as $d) {
    $columns[] = $d["Field"];
}

if(!in_array('similarities', $columns)) {
    $db->query("ALTER TABLE `coreshop_product_filters` ADD `similarities` text NOT NULL;");
}