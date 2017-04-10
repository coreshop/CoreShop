<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Store\Model\Store as BaseStore;

class Store extends BaseStore implements StoreInterface {

    /**
     * @var CurrencyInterface
     */
    private $baseCurrency;

    /**
     * @var CountryInterface
     */
    private $baseCountry;

    /**
     * {@inheritdoc}
     */
    public function getBaseCurrency() {
        return $this->baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseCurrency(CurrencyInterface $baseCurrency) {
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCountry()
    {
        return $this->baseCountry;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseCountry(CountryInterface $baseCountry)
    {
        $this->baseCountry = $baseCountry;
    }
}