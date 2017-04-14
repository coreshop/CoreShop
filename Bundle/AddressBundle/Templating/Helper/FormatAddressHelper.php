<?php

namespace CoreShop\Bundle\AddressBundle\Templating\Helper;

use CoreShop\Component\Address\Formatter\AddressFormatterInterface;

class FormatAddressHelper implements FormatAddressHelperInterface
{
    /**
     * @var AddressFormatterInterface
     */
    private $addressFormatter;

    /**
     * @param AddressFormatterInterface $addressFormatter
     */
    public function __construct(AddressFormatterInterface $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    /**
     * @param $address
     * @param bool $asHtml
     * @return mixed
     */
    public function formatAddress($address, $asHtml = true) {
        return $this->addressFormatter->formatAddress($address, $asHtml);
    }
}
