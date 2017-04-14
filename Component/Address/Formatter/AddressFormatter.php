<?php

namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Pimcore\Model\Address;
use Pimcore\Placeholder;

class AddressFormatter implements AddressFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function formatAddress(Address $address, $asHtml = true) {
        $objectVars = get_object_vars($address);

        $placeHolder = new Placeholder();
        $address = $placeHolder->replacePlaceholders($address->getCountry()->getAddressFormat(), $objectVars);

        if ($asHtml) {
            $address = nl2br($address);
        }

        return $address;
    }
}