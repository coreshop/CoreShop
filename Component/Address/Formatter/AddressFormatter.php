<?php

namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Model\AddressInterface;
use Pimcore\Placeholder;

class AddressFormatter implements AddressFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function formatAddress(AddressInterface $address, $asHtml = true) {
        $objectVars = get_object_vars($address);

        $placeHolder = new Placeholder();
        $address = $placeHolder->replacePlaceholders($address->getCountry()->getAddressFormat(), $objectVars);

        if ($asHtml) {
            $address = nl2br($address);
        }

        return $address;
    }
}