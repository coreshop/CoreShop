<?php

namespace CoreShop\Component\Currency\Context;

use CoreShop\Component\Currency\Model\CurrencyInterface;

interface CurrencyContextInterface
{
    /**
     * @return CurrencyInterface
     *
     * @throws CurrencyNotFoundException
     */
    public function getCurrency();
}
