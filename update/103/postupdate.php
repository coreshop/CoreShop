<?php

$db = \Pimcore\Db::get();

$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_productSpecificPrices'");
$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_customer_groups'");
$db->query("DELETE FROM users_permission_definitions WHERE `key`='coreshop_permission_manufacturers'");

$db->query("UPDATE users_permission_definitions SET `key`='coreshop_permission_product_price_rules' WHERE `key`='coreshop_permission_productPriceRules'");
$db->query("UPDATE users_permission_definitions SET `key`='coreshop_permission_price_rules' WHERE `key`='coreshop_permission_priceRules'");
$db->query("UPDATE users_permission_definitions SET `key`='coreshop_permission_order_states' WHERE `key`='coreshop_permission_orderStates'");

$install = new \CoreShop\Plugin\Install();
$install->createClass('CoreShopProduct', true);
$install->createClass('CoreShopManufacturer', true);

if(file_exists(PIMCORE_TEMPORARY_DIRECTORY . "/manufacturer.tmp")) {
    $mapping = [];

    try {
        $manufacturerSerialized = file_get_contents(PIMCORE_TEMPORARY_DIRECTORY . "/manufacturer.tmp");
        $manufacturer = unserialize($manufacturerSerialized);

        foreach($manufacturer as $man) {
            $newMan = \CoreShop\Model\Manufacturer::create();
            $newMan->setName($man['name']);
            $newMan->setImage(\Pimcore\Model\Asset::getById($man['image']));
            $newMan->setParent(\Pimcore\Model\Object\Service::createFolderByPath("/coreshop/manufacturer"));
            $newMan->setKey(\Pimcore\File::getValidFilename($man['name']));
            $newMan->setPublished(true);
            $newMan->save();

            $mapping[$man['id']] = $newMan;
        }
    }
    catch (\Exception $ex) {

    }
}