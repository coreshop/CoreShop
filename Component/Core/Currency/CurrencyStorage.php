<?php

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Core\Model\Currency;
use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Resource\Storage\StorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CurrencyStorage implements CurrencyStorageInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param StorageInterface $storage
     * @param CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(StorageInterface $storage, CurrencyRepositoryInterface $currencyRepository)
    {
        $this->storage = $storage;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function set(StoreInterface $store, CurrencyInterface $currency)
    {
        if ($this->isBaseCurrency($currency, $store) || !$this->isAvailableCurrency($currency, $store)) {
            $this->storage->remove($this->provideKey($store));

            return;
        }

        $this->storage->set($this->provideKey($store), $currency->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get(StoreInterface $store)
    {
        return $this->currencyRepository->find($this->storage->get($this->provideKey($store)));
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(StoreInterface $store)
    {
        return '_currency_' . $store->getId();
    }

    /**
     * @param CurrencyInterface $currency
     * @param StoreInterface $store
     *
     * @return bool
     */
    private function isBaseCurrency(CurrencyInterface $currency, StoreInterface $store)
    {
        return $store->getBaseCurrency()->getId() === $currency->getId();
    }

    /**
     * @param CurrencyInterface $currency
     * @param StoreInterface $store
     *
     * @return bool
     */
    private function isAvailableCurrency(CurrencyInterface $currency, StoreInterface $store)
    {
        return in_array($currency->getIsoCode(), array_map(function (CurrencyInterface $currency) {
            return $currency->getIsoCode();
        }, $this->getCurrenciesForStore($store)));
    }

    /**
     * @param StoreInterface $store
     * @return array
     */
    private function getCurrenciesForStore(StoreInterface $store) {
        return $this->currencyRepository->findActiveForStore($store);
    }
}
