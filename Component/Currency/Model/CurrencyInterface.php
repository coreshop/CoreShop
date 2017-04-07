<?php

namespace CoreShop\Component\Currency\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface CurrencyInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getIsoCode();

    /**
     * @param mixed $isoCode
     */
    public function setIsoCode($isoCode);

    /**
     * @return int
     */
    public function getNumericIsoCode();

    /**
     * @param int $numericIsoCode
     */
    public function setNumericIsoCode($numericIsoCode);

    /**
     * @return string
     */
    public function getSymbol();

    /**
     * @param string $symbol
     */
    public function setSymbol($symbol);

    /**
     * @return float
     */
    public function getExchangeRate();

    /**
     * @param float $exchangeRate
     */
    public function setExchangeRate($exchangeRate);
}
