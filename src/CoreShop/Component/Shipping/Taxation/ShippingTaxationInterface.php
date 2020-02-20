<?php
declare(strict_types=1);

namespace CoreShop\Component\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;

interface ShippingTaxationInterface
{
    /**
     * @param CartInterface $cart
     * @param CarrierInterface $carrier
     * @param AddressInterface $address
     * @param array $usedTaxes
     * @return array
     */
    public function calculateShippingTax(CartInterface $cart, CarrierInterface $carrier, AddressInterface $address, array $usedTaxes);
}
