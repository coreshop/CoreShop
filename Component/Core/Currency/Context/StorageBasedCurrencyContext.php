<?php

namespace CoreShop\Component\Core\Currency\Context;

use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Core\Currency\CurrencyStorageInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StorageBasedCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @param StoreContextInterface $storeContext
     * @param CurrencyStorageInterface $currencyStorage
     */
    public function __construct(StoreContextInterface $storeContext, CurrencyStorageInterface $currencyStorage)
    {
        $this->storeContext = $storeContext;
        $this->currencyStorage = $currencyStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        $currency = $this->currencyStorage->get($store);

        if (null === $currency) {
            throw CurrencyNotFoundException::notFound($currency);
        }

        return $currency;
    }
}
