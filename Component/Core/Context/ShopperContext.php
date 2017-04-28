<?php

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\RequestBased\CustomerContext;
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
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param StoreContextInterface $storeContext
     * @param CurrencyContextInterface $currencyContext
     * @param LocaleContextInterface $localeContext
     * @param CountryContextInterface $countryContext
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(
        StoreContextInterface $storeContext,
        CurrencyContextInterface $currencyContext,
        LocaleContextInterface $localeContext,
        CountryContextInterface $countryContext,
        CustomerContextInterface $customerContext
    ) {
        $this->storeContext = $storeContext;
        $this->currencyContext = $currencyContext;
        $this->localeContext = $localeContext;
        $this->countryContext = $countryContext;
        $this->customerContext = $customerContext;
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

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customerContext->getCountry();
    }
}
