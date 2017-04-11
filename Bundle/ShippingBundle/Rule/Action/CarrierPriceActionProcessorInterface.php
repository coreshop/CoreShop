<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

interface CarrierPriceActionProcessorInterface
{
    /**
     * @param CarrierInterface $carrier
     * @param CartInterface $cart
     * @param AddressInterface $address
     * @param array $configuration
     * @return mixed
     */
    public function getModification(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration);

    /**
     * @param CarrierInterface $carrier
     * @param CartInterface $cart
     * @param AddressInterface $address
     * @param array $configuration
     * @return mixed
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration);
}
