
<?php

$db = \Pimcore\Db::get();

$db->query("CREATE TABLE `coreshop_carrier_shippingrules` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) DEFAULT NULL,
`description` text,
`conditions` text,
`actions` text,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$db->query("CREATE TABLE `coreshop_carrier_shippingrule_groups` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `carrierId` int NOT NULL,
  `priority` int NOT NULL,
  `shippingRuleId` int NOT NULL
);");

//Convert current Shipping Rules (weight, price) to new Rules
$carriers = \CoreShop\Model\Carrier::getList();
$carriers->load();

foreach($carriers as $carrier) {
    if($carrier instanceof \CoreShop\Model\Carrier) {
        $type = $carrier->getShippingMethod();

        foreach($carrier->getRanges() as $range) {
            $class = null;

            if($type === 'price') {
                $class = new \CoreShop\Model\Carrier\ShippingRule\Condition\Amount();
                $class->setMinAmount($range->getDelimiter1());
                $class->setMaxAmount($range->getDelimiter2());
            }
            else {
                $class = new \CoreShop\Model\Carrier\ShippingRule\Condition\Weight();
                $class->setMinWeight($range->getDelimiter1());
                $class->setMaxWeight($range->getDelimiter2());
            }

            foreach($range->getPrices() as $deliveryPrice) {
                if($deliveryPrice instanceof \CoreShop\Model\Carrier\DeliveryPrice) {
                    $zoneCondition = new \CoreShop\Model\Carrier\ShippingRule\Condition\Zones();
                    $zoneCondition->setZones([$deliveryPrice->getZoneId()]);

                    $price = new \CoreShop\Model\Carrier\ShippingRule\Action\FixedPrice();
                    $price->setFixedPrice($deliveryPrice->getPrice());

                    if($deliveryPrice->getPrice() > 0) {
                        $rule = new \CoreShop\Model\Carrier\ShippingRule();
                        $rule->setConditions([$class, $zoneCondition]);
                        $rule->setActions([$price]);
                        $rule->setName($carrier->getName() . "-" . $deliveryPrice->getZone()->getName() . "-" . $range->getDelimiter1());
                        $rule->save();
                    }
                }
            }
        }
    }
}