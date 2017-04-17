<?php

namespace CoreShop\Bundle\ShippingBundle\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

interface ShippingRuleActionProcessorInterface {

    /**
     * @param ShippingRuleInterface $shippingRule
     * @param CarrierInterface $carrier
     * @param AddressInterface $address
     * @param bool $withTax
     * @return mixed
     */
    public function getPrice(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, AddressInterface $address, $withTax = true);

    /**
     * @param ShippingRuleInterface $shippingRule
     * @param CarrierInterface $carrier
     * @param AddressInterface $address
     * @param $price
     * @return mixed
     */
    public function getModification(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, AddressInterface $address, $price);

}