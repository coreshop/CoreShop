<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Store\Model\StoreInterface as BaseStoreInterface;

interface StoreInterface extends BaseStoreInterface {

    /**
     * @return CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * @param CurrencyInterface $baseCurrency
     */
    public function setBaseCurrency(CurrencyInterface $baseCurrency);
}