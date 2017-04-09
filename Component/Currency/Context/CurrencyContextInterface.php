<?php

namespace CoreShop\Component\Currency\Context;

interface CurrencyContextInterface
{
    /**
     * @return string
     *
     * @throws CurrencyNotFoundException
     */
    public function getCurrencyCode();
}
