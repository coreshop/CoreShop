<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Store\Model\Store as BaseStore;

class Store extends BaseStore implements StoreInterface {

    /**
     * @var CurrencyInterface
     */
    private $baseCurrency;

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
}