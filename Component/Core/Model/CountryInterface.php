<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\CountryInterface as BaseCountryInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\Common\Collections\Collection;

interface CountryInterface extends BaseCountryInterface
{
    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     *
     * @return static
     */
    public function setCurrency($currency);

    /**
     * @return Collection|StoreInterface[]
     */
    public function getStores();

    /**
     * @return bool
     */
    public function hasStores();

    /**
     * @param StoreInterface $store
     */
    public function addStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     */
    public function removeStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function hasStore(StoreInterface $store);
}
