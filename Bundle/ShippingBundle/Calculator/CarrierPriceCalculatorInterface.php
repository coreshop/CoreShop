<?php

namespace CoreShop\Bundle\ShippingBundle\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

interface CarrierPriceCalculatorInterface
{
    /**
     * @param CarrierInterface $carrier
     * @param CartInterface $cart
     * @param AddressInterface $address
     * @param boolean $withTax
     * @return mixed
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true);
}