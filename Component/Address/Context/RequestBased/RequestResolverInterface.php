<?php

namespace CoreShop\Component\Address\Context\RequestBased;

use CoreShop\Component\Core\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestResolverInterface
{
    /**
     * @param Request $request
     *
     * @return CountryInterface|null
     */
    public function findCountry(Request $request);
}
