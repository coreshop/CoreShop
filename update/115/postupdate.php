<?php

$list = \CoreShop\Model\Index::getList();
$list->load();

$db = \Pimcore\Db::get();

foreach($list->getData() as $index) {
    if ($index instanceof \CoreShop\Model\Index) {
        if ($index->getType() === "mysql") {
            $index->getWorker()->createOrUpdateIndexStructures(); //Creates localized index-views
        }
    }
}