<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Currency\Model;

use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Resource\Model\AbstractResource;
use Doctrine\Common\Collections\Collection;

class Currency extends AbstractResource implements CurrencyInterface
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $isoCode;

    /**
     * @var int
     */
    public $numericIsoCode;

    /**
     * @var string
     */
    public $symbol;

    /**
     * @var float
     */
    public $exchangeRate;

    /**
     * @var Collection|CountryInterface[]
     */
    protected $countries;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param mixed $isoCode
     * @return static
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param int $numericIsoCode
     * @return static
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     * @return static
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
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
     * @return static
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCountries()
    {
        return !$this->countries->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addCountry(CountryInterface $country)
    {
        if (!$this->hasCountry($country)) {
            $this->countries->add($country);
            $country->setCurrency($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCountry(CountryInterface $country)
    {
        if ($this->hasCountry($country)) {
            $this->countries->removeElement($country);
            $country->setCurrency(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCountry(CountryInterface $country)
    {
        return $this->countries->contains($country);
    }
}
