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
     * @param StoreContextInterface $storeContext
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(
        StoreContextInterface $storeContext,
        CurrencyContextInterface $currencyContext
    ) {
        $this->storeContext = $storeContext;
        $this->currencyContext = $currencyContext;
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
}
