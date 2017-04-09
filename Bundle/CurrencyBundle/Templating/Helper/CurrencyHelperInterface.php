<?php

namespace CoreShop\Bundle\CurrencyBundle\Templating\Helper;

interface CurrencyHelperInterface
{
    /**
     * @param string $code
     *
     * @return string
     */
    public function convertCurrencyCodeToSymbol($code);
}
