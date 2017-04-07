<?php

namespace CoreShop\Bundle\ProductBundle\Rule\Action;

use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;

class NewPriceActionProcessor implements ProductPriceActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDiscount($subject, array $configuration, $withTax = true)
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
