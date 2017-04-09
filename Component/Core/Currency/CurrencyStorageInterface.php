<?php

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;

interface CurrencyStorageInterface
{
    /**
     * @param StoreInterface $store
     * @param string $currencyCode
     */
    public function set(StoreInterface $store, $currencyCode);

    /**
     * @param StoreInterface $store
     *
     * @return string
     *
     * @throws CurrencyNotFoundException
     */
    public function get(StoreInterface $store);
}
