<?php

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
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
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param StorageInterface $storage
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(StorageInterface $storage, CountryRepositoryInterface $countryRepository)
    {
        $this->storage = $storage;
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function set(StoreInterface $store, $currencyCode)
    {
        if ($this->isBaseCurrency($currencyCode, $store) || !$this->isAvailableCurrency($currencyCode, $store)) {
            $this->storage->remove($this->provideKey($store));

            return;
        }

        $this->storage->set($this->provideKey($store), $currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function get(StoreInterface $store)
    {
        return $this->storage->get($this->provideKey($store));
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(StoreInterface $store)
    {
        return '_currency_' . $store->getId();
    }

    /**
     * @param string$currencyCode
     * @param StoreInterface $store
     *
     * @return bool
     */
    private function isBaseCurrency($currencyCode, StoreInterface $store)
    {
        //TODO:
        return true;
    }

    /**
     * @param string $currencyCode
     * @param StoreInterface $store
     *
     * @return bool
     */
    private function isAvailableCurrency($currencyCode, StoreInterface $store)
    {
        return in_array($currencyCode, $this->getCurrenciesForStore($store), true);
    }

    /**
     * @param StoreInterface $store
     * @return array
     */
    private function getCurrenciesForStore(StoreInterface $store) {
        $countries = $this->countryRepository->findForStore($store);
        $currencies = [];

        foreach ($countries as $country) {
            $currencies[]  = $country->getCurrency()->getId();
        }

        return $currencies;
    }
}
