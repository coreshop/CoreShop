<?php

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\Request;

final class StoreBasedCountryResolver implements RequestResolverInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    public function __construct(StoreContextInterface $storeContext)
    {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        $store = $this->storeContext->getStore();

        if ($store instanceof StoreInterface)
            return $store->getBaseCountry();

        throw new CountryNotFoundException();
    }
}
