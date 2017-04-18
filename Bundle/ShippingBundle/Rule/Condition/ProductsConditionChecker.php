<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

class ProductsConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $cartItems = $cart->getItems();

        foreach ($cartItems as $item) {
            if ($item->getProduct() instanceof ProductInterface) {
                if (in_array($item->getProduct()->getId(), $configuration['products'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
