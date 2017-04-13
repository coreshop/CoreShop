<?php

namespace CoreShop\Bundle\ShippingBundle\Calculator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

class CompositePriceCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * @var CarrierPriceCalculatorInterface[]
     */
    protected $calculators;

    /**
     * @param CarrierPriceCalculatorInterface[] $calculators
     */
    public function __construct(array $calculators)
    {
        $this->calculators = $calculators;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true)
    {
        $price = false;

        /**
         * First Price wins
         */
        foreach ($this->calculators as $calculator) {
            $actionPrice = $calculator->getPrice($carrier, $cart, $address, $withTax);

            if (false !== $actionPrice && null !== $actionPrice) {
                $price = $actionPrice;
                break;
            }
        }

        return $price;
    }
}
