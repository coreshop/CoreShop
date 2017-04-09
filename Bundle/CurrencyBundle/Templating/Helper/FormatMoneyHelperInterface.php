<?php

namespace CoreShop\Bundle\CurrencyBundle\Templating\Helper;

interface FormatMoneyHelperInterface
{
    /**
     * @param int $amount
     * @param string $currencyCode
     * @param string $localeCode
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function formatAmount($amount, $currencyCode, $localeCode);
}
