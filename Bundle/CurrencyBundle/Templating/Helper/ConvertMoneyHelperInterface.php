<?php

namespace CoreShop\Bundle\CurrencyBundle\Templating\Helper;

interface ConvertMoneyHelperInterface
{
    /**
     * @param int $amount
     * @param string|null $sourceCurrencyCode
     * @param string|null $targetCurrencyCode
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertAmount($amount, $sourceCurrencyCode, $targetCurrencyCode);
}
