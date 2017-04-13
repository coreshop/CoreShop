<?php

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

interface ShopperContextInterface extends
    StoreContextInterface,
    CurrencyContextInterface,
    LocaleContextInterface,
    CountryContextInterface
{
}
