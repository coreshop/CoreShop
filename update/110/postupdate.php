<?php

$db = \Pimcore\Db::get();

$indexes = \CoreShop\Model\Index::getList();
$indexes->load();

foreach($indexes->getData() as $index) {
    if($index instanceof  \CoreShop\Model\Index) {
        if ($index->getType() === "mysql") {
            $worker = $index->getWorker();

            if($worker instanceof \CoreShop\IndexService\Mysql) {
                $tableName = $worker->getTablename();

                $db->query("ALTER TABLE `$tableName` ADD COLUMN `minPrice` double NOT NULL AFTER `shops`, ADD COLUMN`maxPrice` double NOT NULL AFTER `shops`;");
            }
        }
    }
}