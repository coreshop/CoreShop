<?php

$install = new \CoreShop\Plugin\Install();
$install->createFieldCollection('CoreShopPriceRuleItem');
$install->createClass('CoreShopOrder');
$install->createClass('CoreShopCart');

//Migrate all Orders
$list = \CoreShop\Model\Order::getList();

foreach($list->getObjects() as $object) {
    $priceRule = $object->getPriceRule();

    if($object instanceof \CoreShop\Model\Order) {
        if ($priceRule instanceof \CoreShop\Model\Cart\PriceRule) {
            $item = \CoreShop\Model\PriceRule\Item::create();

            $item->setPriceRule($priceRule);
            $item->setVoucherCode($priceRule->getCode());
            $item->setDiscount($object->getDiscount());

            $fieldCollection = new \Pimcore\Model\Object\Fieldcollection();
            $fieldCollection->add($item);

            $object->setPriceRuleFieldCollection($fieldCollection);
            $object->save();
        }
    }
}