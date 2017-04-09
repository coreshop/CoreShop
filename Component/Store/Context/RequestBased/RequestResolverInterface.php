<?php

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Model\StoreInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestResolverInterface
{
    /**
     * @param Request $request
     *
     * @return StoreInterface|null
     */
    public function findStore(Request $request);
}
