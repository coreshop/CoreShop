<?php
declare(strict_types=1);

namespace CoreShop\Component\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;

class ShippingTaxationCartItems implements ShippingTaxationInterface
{
    /**
     * @inheritDoc
     */
    public function calculateShippingTax(
        CartInterface $cart,
        CarrierInterface $carrier,
        AddressInterface $address,
        array $usedTaxes
    ) {
        // TODO: Implement calculateTaxation() method.
    }
}
