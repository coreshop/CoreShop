<?php

$list = \CoreShop\Model\Index::getList();
$list->load();

$db = \Pimcore\Db::get();

foreach($list->getData() as $index) {
    if($index instanceof \CoreShop\Model\Index) {
        if($index->getType() === "mysql") {
            $worker = $index->getWorker();
            if($worker instanceof \CoreShop\IndexService\Mysql) {
                $tableName = $worker->getTablename();

                $db->query("ALTER TABLE `$tableName`
                  ADD `o_key` varchar(255) NOT NULL AFTER `o_id`;
                ");
            }
        }
    }
}