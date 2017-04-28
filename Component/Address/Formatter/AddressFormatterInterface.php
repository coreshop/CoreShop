<?php

namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Model\AddressInterface;

interface AddressFormatterInterface {
    /**
     * @param AddressInterface $address
     * @param boolean $asHtml
     * @return mixed
     */
    public function formatAddress(AddressInterface $address, $asHtml = true);

}