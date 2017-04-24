<?php

namespace CoreShop\Bundle\ShippingBundle\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

final class CarrierPriceCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $shippingCalculatorRegistry;

    /**
     * ProductPriceCalculator constructor.
     * @param PrioritizedServiceRegistryInterface $shippingCalculatorRegistry
     */
    public function __construct(PrioritizedServiceRegistryInterface $shippingCalculatorRegistry)
    {
        $this->shippingCalculatorRegistry = $shippingCalculatorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true)
    {
        foreach ($this->shippingCalculatorRegistry->all() as $calculator) {
            $price = $calculator->getPrice($carrier, $cart, $address, $withTax);

            if (false !== $price && null !== $price) {
                return $price;
            }
        }

        return 0;
    }
}
