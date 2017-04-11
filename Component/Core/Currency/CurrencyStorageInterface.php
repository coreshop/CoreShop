<?php

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;

interface CurrencyStorageInterface
{
    /**
     * @param StoreInterface $store
     * @param CurrencyInterface $currency
     */
    public function set(StoreInterface $store, CurrencyInterface $currency);

    /**
     * @param StoreInterface $store
     *
     * @return string
     *
     * @throws CurrencyNotFoundException
     */
    public function get(StoreInterface $store);
}
