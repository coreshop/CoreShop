<?php

$db = \Pimcore\Db::get();

$indexes = \CoreShop\Model\Index::getList();
$indexes->load();

foreach ($indexes->getData() as $index) {
    if ($index instanceof  \CoreShop\Model\Index) {
        if ($index->getType() === "mysql") {
            $worker = $index->getWorker();

            if ($worker instanceof \CoreShop\IndexService\Mysql) {
                $tableName = $worker->getTablename();

                $db->query("ALTER TABLE `$tableName` ADD COLUMN `minPrice` double NOT NULL AFTER `shops`, ADD COLUMN`maxPrice` double NOT NULL AFTER `shops`;");
            }
        }
    }
}

$db->query("ALTER TABLE `coreshop_product_filters` ADD `useShopPagingSettings` int(11) NULL DEFAULT '0';");

$listModeDefault = \CoreShop\Model\Configuration::set("SYSTEM.CATEGORY.LIST.MODE", "list");
$gridPerPageAllowed = \CoreShop\Model\Configuration::set("SYSTEM.CATEGORY.GRID.PER_PAGE", [5, 10, 15, 20, 25]);
$gridPerPageDefault = \CoreShop\Model\Configuration::set("SYSTEM.CATEGORY.GRID.PER_PAGE_DEFAULT", 10);
$listPerPageAllowed = \CoreShop\Model\Configuration::set("SYSTEM.CATEGORY.LIST.PER_PAGE", [12, 24, 36]);
$listPerPageDefault = \CoreShop\Model\Configuration::set("SYSTEM.CATEGORY.LIST.PER_PAGE_DEFAULT", 12);
