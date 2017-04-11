<?php

namespace CoreShop\Bundle\ShippingBundle\Checker;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

interface CarrierShippingRuleCheckerInterface {
    /**
     * @param CarrierInterface $carrier
     * @param CartInterface $cart
     * @param AddressInterface $address
     * @return mixed
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address);
}