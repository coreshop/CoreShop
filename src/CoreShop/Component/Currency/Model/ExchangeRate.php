<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Currency\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
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

    public function __construct(
        ) {
        $this->creationDate = new \DateTime();
    }

    public function getId(): ?int
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

    public function setFromCurrency(CurrencyInterface $currency)
    {
        $this->fromCurrency = $currency;
    }

    /**
     * @return CurrencyInterface
     */
    public function getToCurrency()
    {
        return $this->toCurrency;
    }

    public function setToCurrency(CurrencyInterface $currency)
    {
        $this->toCurrency = $currency;
    }
}
