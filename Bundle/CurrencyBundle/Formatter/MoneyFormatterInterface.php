<?php

namespace CoreShop\Bundle\CurrencyBundle\Formatter;

interface MoneyFormatterInterface
{
    /**
     * @param int $amount
     * @param string $currencyCode
     * @param string $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function format($amount, $currencyCode, $locale = 'en');
}
