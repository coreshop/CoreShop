<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\CartInterface as BaseCartInterface;

interface CartInterface extends BaseCartInterface
{
    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency($currency);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param \CoreShop\Component\Store\Model\StoreInterface $store
     */
    public function setStore($store);

    /**
     * @return StoreInterface
     */
    public function getStore();
}