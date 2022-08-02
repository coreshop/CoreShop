<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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

    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

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
