<?php

namespace CoreShop\Component\Currency\Repository;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface ExchangeRateRepositoryInterface extends RepositoryInterface
{
    /**
     * @param CurrencyInterface $fromCurrency
     * @param CurrencyInterface $toCurrency
     *
     * @return ExchangeRateInterface|null
     */
    public function findOneWithCurrencyPair(CurrencyInterface $fromCurrency, CurrencyInterface $toCurrency);
}
