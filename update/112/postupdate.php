<?php

$install = new \CoreShop\Plugin\Install();

$install->createClass('CoreShopCart');

$install->createClass('CoreShopOrderItem');
$install->createClass('CoreShopOrder');

$install->createClass('CoreShopOrderInvoiceItem');
$install->createClass('CoreShopOrderInvoice');

$install->createStaticRoutes();

$db = \Pimcore\Db::get();
$sql = "SELECT cid FROM properties WHERE `name`='invoice'";

$objects = $db->fetchAll($sql);

foreach($objects as $objectRaw) {
    $id = $objectRaw['cid'];

    $object = \Pimcore\Model\Object\AbstractObject::getById($id);

    if($object instanceof \CoreShop\Model\Order) {
        $object->createInvoiceForAllItems();
    }
}
