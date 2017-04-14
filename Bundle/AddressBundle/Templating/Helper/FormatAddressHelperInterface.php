<?php

namespace CoreShop\Bundle\AddressBundle\Templating\Helper;

interface FormatAddressHelperInterface
{
    /**
     * @param $address
     * @param bool $asHtml
     * @return mixed
     */
    public function formatAddress($address, $asHtml = true);
}
