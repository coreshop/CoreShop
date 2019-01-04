<?php

namespace CoreShop\Component\Currency\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class ExchangeRate implements ExchangeRateInterface
{
    use SetValuesTrait;
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var float
     */
    protected $exchangeRate;

    /**
     * @var CurrencyInterface
     */
    protected $fromCurrency;

    /**
     * @var CurrencyInterface
     */
    protected $toCurrency;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param float $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * @return CurrencyInterface
     */
    public function getFromCurrency()
    {
        return $this->fromCurrency;
    }

    /**
     * @param CurrencyInterface $fromCurrency
     */
    public function setFromCurrency(CurrencyInterface $fromCurrency)
    {
        $this->fromCurrency = $fromCurrency;
    }

    /**
     * @return CurrencyInterface
     */
    public function getToCurrency()
    {
        return $this->toCurrency;
    }

    /**
     * @param CurrencyInterface $toCurrency
     */
    public function setToCurrency(CurrencyInterface $toCurrency)
    {
        $this->toCurrency = $toCurrency;
    }
}
