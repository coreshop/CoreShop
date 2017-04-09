<?php

namespace CoreShop\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use CoreShop\Component\Currency\Model\CurrencyInterface as BaseCurrencyInterface;

interface CurrencyInterface extends BaseCurrencyInterface
{
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
