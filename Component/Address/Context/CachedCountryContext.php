<?php

namespace CoreShop\Component\Address\Context;

use CoreShop\Component\Address\Model\CountryInterface;

final class CachedCountryContext implements CountryContextInterface
{
    /**
     * @var CountryInterface
     */
    private $country = null;

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        if ($this->country instanceof CountryInterface) {
            return $this->country;
        }

        throw new CountryNotFoundException();
    }

    /**
     * @param CountryInterface $country
     */
    public function setCountry(CountryInterface $country) {
        $this->country = $country;
    }
}
