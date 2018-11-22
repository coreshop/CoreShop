<?php

namespace CoreShop\Component\Currency\Model;

trait CurrencyAwareTrait
{
    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @return CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
