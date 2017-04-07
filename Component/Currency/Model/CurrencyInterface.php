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
use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

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

    /**
     * @return Collection|CountryInterface[]
     */
    public function getCountries();

    /**
     * @return bool
     */
    public function hasCountries();

    /**
     * @param CountryInterface $country
     */
    public function addCountry(CountryInterface $country);

    /**
     * @param CountryInterface $country
     */
    public function removeCountry(CountryInterface $country);

    /**
     * @param CountryInterface $country
     *
     * @return bool
     */
    public function hasCountry(CountryInterface $country);
}
