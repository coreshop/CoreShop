<?php

namespace CoreShop\Component\Address\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait CountriesAwareTrait
{
    /**
     * @var Collection|CountryInterface[]
     */
    protected $countries;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
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
    public function addCountry(CountryInterface $store)
    {
        if (!$this->hasCountry($store)) {
            $this->countries->add($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCountry(CountryInterface $store)
    {
        if ($this->hasCountry($store)) {
            $this->countries->removeElement($store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCountry(CountryInterface $store)
    {
        return $this->countries->contains($store);
    }
}