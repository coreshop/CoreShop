<?php

namespace CoreShop\Bundle\ShippingBundle\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;

interface CarrierPriceActionProcessorInterface
{
    /**
     * @param array $configuration
     * @return mixed
     */
    public function getPrice(array $configuration);

    /**
     * @param $price
     * @param array $configuration
     * @return mixed
     */
    public function getModification($price, array $configuration);
}
