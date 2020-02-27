<?php
declare(strict_types=1);

namespace CoreShop\Component\Shipping\Taxation;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\Carrier;
use CoreShop\Component\Core\Model\CartInterface;

interface TaxCalculationStrategyInterface
{
    /**
     * @param CartInterface $cart
     * @param Carrier $carrier
     * @param AddressInterface $address
     * @param array $usedTaxes
     * @return array
     */
    public function calculateShippingTax(CartInterface $cart, Carrier $carrier, AddressInterface $address, array $usedTaxes);
}
