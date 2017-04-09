<?php

namespace CoreShop\Component\Core\Context;

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
     * @param StoreContextInterface $storeContext
     * @param CurrencyContextInterface $currencyContext
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        StoreContextInterface $storeContext,
        CurrencyContextInterface $currencyContext,
        LocaleContextInterface $localeContext
    ) {
        $this->storeContext = $storeContext;
        $this->currencyContext = $currencyContext;
        $this->localeContext = $localeContext;
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
    public function getCurrencyCode()
    {
        return $this->currencyContext->getCurrencyCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        return $this->localeContext->getLocaleCode();
    }
}
