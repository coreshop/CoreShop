<?php

$list = \CoreShop\Model\Manufacturer::getList();

$file = PIMCORE_TEMPORARY_DIRECTORY . "/manufacturer.tmp";
$all = [];

foreach($list->getData() as $manufacturer) {
    $vars = get_object_vars($manufacturer);

    $all[] = $vars;
}

file_put_contents($file, serialize($all));