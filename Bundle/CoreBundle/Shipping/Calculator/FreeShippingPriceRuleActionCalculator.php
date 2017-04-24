<?php

namespace CoreShop\Bundle\CoreBundle\Shipping\Calculator;

use CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;

class FreeShippingPriceRuleActionCalculator implements CarrierPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, $withTax = true)
    {
        if ($cart->hasPriceRules()) {
            foreach ($cart->getPriceRules() as $priceRule) {
                if ($priceRule instanceof CartPriceRuleInterface) {
                    foreach ($priceRule->getActions() as $action) {
                        if ($action->getType() === 'freeShipping')
                            return 0;
                    }
                }
            }
        }

        return false;
    }
}
