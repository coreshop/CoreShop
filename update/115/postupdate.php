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

//Remove ORDERSTATES from config file
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.QUEUE');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.PAYMENT');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.PREPERATION');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.SHIPPING');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.DELIVERED');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.CANCELED');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.REFUND');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.ERROR');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.OUTOFSTOCK');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.BANKWIRE');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.OUTOFSTOCK_UNPAID');
\CoreShop\Model\Configuration::remove('SYSTEM.ORDERSTATE.COD');

//alter table && and extend!
$db->query("ALTER TABLE `coreshop_orderstates` ADD `system` tinyint(1) NOT NULL DEFAULT '0' AFTER `email`;");
$db->query("ALTER TABLE `coreshop_orderstates` ADD `identifier` varchar(255) DEFAULT NULL AFTER `color`;");

$states = [
    1  => 'QUEUE',
    2  => 'PAYMENT',
    3  => 'PREPERATION',
    4  => 'SHIPPING',
    5  => 'DELIVERED',
    6  => 'CANCELED',
    7  => 'REFUND',
    8  => 'ERROR',
    9  => 'OUTOFSTOCK',
    10 => 'BANKWIRE',
    11 => 'OUTOFSTOCK_UNPAID',
    12 => 'COD',
];

foreach( $states as $dbId => $identifier) {
    $db->query("UPDATE `coreshop_orderstates` SET `system` = 1, `identifier` = '" . $identifier . "' WHERE id = " . $dbId . ";");
}