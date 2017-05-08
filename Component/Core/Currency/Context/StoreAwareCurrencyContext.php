<?php

namespace CoreShop\Component\Core\Currency\Context;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

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
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param StoreContextInterface $storeContext
     * @param CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(
        CurrencyContextInterface $currencyContext,
        StoreContextInterface $storeContext,
        CurrencyRepositoryInterface $currencyRepository
    )
    {
        $this->currencyContext = $currencyContext;
        $this->storeContext = $storeContext;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        try {
            $currency = $this->currencyContext->getCurrency();

            if (!$currency instanceof CurrencyInterface || !$this->isAvailableCurrency($currency, $store)) {
                return $store->getBaseCurrency();
            }

            return $currency;
        } catch (CurrencyNotFoundException $exception) {
            return $store->getBaseCurrency();
        }
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
