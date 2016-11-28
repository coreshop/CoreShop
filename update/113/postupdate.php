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

$install = new \CoreShop\Plugin\Install();

$install->createClass('CoreShopOrderShipmentItem');
$install->createClass('CoreShopOrderShipment');

\CoreShop\Model\Configuration::set('SYSTEM.SHIPMENT.PREFIX', 'OS');
\CoreShop\Model\Configuration::set('SYSTEM.SHIPMENT.SUFFIX', '');
\CoreShop\Model\Configuration::set('SYSTEM.SHIPMENT.WKHTML', '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5');

//Should we check for Order-States and create Shipments for specific states?