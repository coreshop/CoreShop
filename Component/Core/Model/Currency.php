<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Currency\Model\Currency as BaseCurrency;
use Doctrine\Common\Collections\Collection;

class Currency extends BaseCurrency implements CurrencyInterface
{
    /**
     * @var Collection|CountryInterface[]
     */
    protected $countries;

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
