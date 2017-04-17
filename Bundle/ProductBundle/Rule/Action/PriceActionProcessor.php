<?php

namespace CoreShop\Bundle\ProductBundle\Rule\Action;

use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;

class PriceActionProcessor implements ProductPriceActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, $price, array $configuration)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($subject, array $configuration)
    {
        return $configuration['price'];
    }
}
