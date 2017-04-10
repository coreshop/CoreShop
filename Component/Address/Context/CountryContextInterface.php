<?php

namespace CoreShop\Component\Address\Context;

use CoreShop\Component\Address\Model\CountryInterface;

interface CountryContextInterface
{
    /**
     * @return CountryInterface
     *
     * @throws CountryNotFoundException
     */
    public function getCountry();
}
