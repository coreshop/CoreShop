<?php

namespace CoreShop\Bundle\ShippingBundle\Processor;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;

interface CartCarrierProcessorInterface {

    /**
     * @param CartInterface $cart
     * @param AddressInterface|null $address
     * @return mixed
     */
    public function getCarriersForCart(CartInterface $cart, AddressInterface $address = null);

}