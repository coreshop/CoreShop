<?php

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface as BaseCurrencyRepositoryInterface;

interface CurrencyRepositoryInterface extends BaseCurrencyRepositoryInterface
{
    /**
     * @param StoreInterface $store
     * @return CurrencyInterface[]
     */
    public function findActiveForStore(StoreInterface $store);
}
