<?php

namespace CoreShop\Component\Currency\Converter;

interface CurrencyConverterInterface
{
    /**
     * @param int $value
     * @param string $sourceCurrencyCode
     * @param string $targetCurrencyCode
     *
     * @return int
     */
    public function convert($value, $sourceCurrencyCode, $targetCurrencyCode);
}
