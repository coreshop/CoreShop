<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

class CategoriesConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $cartItems = $cart->getItems();

        foreach ($cartItems as $item) {
            if ($item->getProduct() instanceof ProductInterface) {
                foreach ($item->getProduct()->getCategories() as $category) {
                    if ($category instanceof ResourceInterface) {
                        if (in_array($category->getId(), $configuration['categories'])) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
