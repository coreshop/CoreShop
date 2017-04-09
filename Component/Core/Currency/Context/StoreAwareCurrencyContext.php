<?php

namespace CoreShop\Component\Core\Currency\Context;

use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;

final class StoreAwareCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param StoreContextInterface $storeContext
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(
        CurrencyContextInterface $currencyContext,
        StoreContextInterface $storeContext,
        CountryRepositoryInterface $countryRepository
    )
    {
        $this->currencyContext = $currencyContext;
        $this->storeContext = $storeContext;
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        try {
            $currencyCode = $this->currencyContext->getCurrencyCode();

            if (!$this->isAvailableCurrency($currencyCode, $store)) {
                return $store->getBaseCurrency()->getIsoCode();
            }

            return $currencyCode;
        } catch (CurrencyNotFoundException $exception) {
            return $store->getBaseCurrency()->getIsoCode();
        }
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
