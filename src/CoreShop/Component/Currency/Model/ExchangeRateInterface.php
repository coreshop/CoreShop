<?php

namespace CoreShop\Component\Currency\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ExchangeRateInterface extends ResourceInterface
{
    /**
     * @return float
     */
    public function getExchangeRate();

    /**
     * @param float $exchangeRate
     */
    public function setExchangeRate($exchangeRate);

    /**
     * @return CurrencyInterface
     */
    public function getFromCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setFromCurrency(CurrencyInterface $currency);

    /**
     * @return CurrencyInterface
     */
    public function getToCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setToCurrency(CurrencyInterface $currency);
}
