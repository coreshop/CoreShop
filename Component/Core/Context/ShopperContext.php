<?php

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

class ShopperContext implements ShopperContextInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var CountryContextInterface
     */
    private $countryContext;

    /**
     * @param StoreContextInterface $storeContext
     * @param CurrencyContextInterface $currencyContext
     * @param LocaleContextInterface $localeContext
     * @param CountryContextInterface $countryContext
     */
    public function __construct(
        StoreContextInterface $storeContext,
        CurrencyContextInterface $currencyContext,
        LocaleContextInterface $localeContext,
        CountryContextInterface $countryContext
    ) {
        $this->storeContext = $storeContext;
        $this->currencyContext = $currencyContext;
        $this->localeContext = $localeContext;
        $this->countryContext = $countryContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->storeContext->getStore();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currencyContext->getCurrency();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        return $this->localeContext->getLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->countryContext->getCountry();
    }
}
