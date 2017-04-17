<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

class DiscountPercentActionProcessor implements CarrierPriceActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, AddressInterface $address, array $configuration, $withTax = true)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getModification(CarrierInterface $carrier, AddressInterface $address, $price, array $configuration)
    {
        return -1 * ($price * ($configuration['percent'] / 100));
    }
}