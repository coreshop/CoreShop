<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Action;

class PriceActionProcessor implements CarrierPriceActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(array $configuration)
    {
        return $configuration['price'];
    }

    public function getModification($price, array $configuration)
    {
        return 0;
    }
}