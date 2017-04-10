<?php

namespace CoreShop\Component\Core\Currency;

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
        return $store->getBaseCurrency()->getIsoCode() === $currencyCode;
    }

    /**
     * @param string $currencyCode
     * @param StoreInterface $store
     *
     * @return bool
     */
    private function isAvailableCurrency($currencyCode, StoreInterface $store)
    {
        return in_array($currencyCode, array_map(function (CurrencyInterface $currency) {
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
