<?php

namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Pimcore\Model\Address;

interface AddressFormatterInterface {
    /**
     * @param Address $address
     * @param boolean $asHtml
     * @return mixed
     */
    public function formatAddress(Address $address, $asHtml = true);

}