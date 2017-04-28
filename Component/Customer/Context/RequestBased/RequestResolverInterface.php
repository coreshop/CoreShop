<?php

namespace CoreShop\Component\Customer\Context\RequestBased;

use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestResolverInterface
{
    /**
     * @param Request $request
     *
     * @return CustomerInterface|null
     */
    public function findCustomer(Request $request);
}
