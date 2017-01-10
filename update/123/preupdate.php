<?php

$list = \CoreShop\Model\Index::getList();
$file = PIMCORE_TEMPORARY_DIRECTORY . "/indexes.tmp";
$all = [];

foreach($list->getData() as $index) {
    $vars = get_object_vars($index);

    $all[] = $vars;
}

file_put_contents($file, serialize($all));
