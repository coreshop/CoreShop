<?php

namespace CoreShop\Component\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;

interface CarrierPriceActionProcessorInterface
{
    /**
     * @param CarrierInterface $carrier
     * @param AddressInterface $address
     * @param array $configuration
     * @param boolean $withTax
     * @return mixed
     */
    public function getPrice(CarrierInterface $carrier, AddressInterface $address, array $configuration, $withTax = true);

    /**
     * @param CarrierInterface $carrier
     * @param AddressInterface $address
     * @param $price
     * @param array $configuration
     * @return mixed
     */
    public function getModification(CarrierInterface $carrier, AddressInterface $address, $price, array $configuration);
}
