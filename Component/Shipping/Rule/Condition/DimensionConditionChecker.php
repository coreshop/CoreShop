<?php

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;

class DimensionConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $height = $configuration['height'];
        $width = $configuration['width'];
        $depth = $configuration['depth'];

        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                if ($height > 0) {
                    if ($product->getHeight() > $height) {
                        return false;
                    }
                }

                if ($depth > 0) {
                    if ($product->getDepth() > $depth) {
                        return false;
                    }
                }

                if ($width > 0) {
                    if ($product->getWidth() > $width) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
