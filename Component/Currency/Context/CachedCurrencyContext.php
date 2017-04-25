<?php

namespace CoreShop\Component\Currency\Context;

use CoreShop\Component\Currency\Model\CurrencyInterface;

final class CachedCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var CurrencyInterface
     */
    private $currency = null;

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        if ($this->currency instanceof CurrencyInterface) {
            return $this->currency;
        }

        throw new CurrencyNotFoundException();
    }
}
