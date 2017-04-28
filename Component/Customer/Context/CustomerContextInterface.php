<?php

namespace CoreShop\Component\Customer\Context;

use CoreShop\Component\Store\Model\StoreInterface;

interface CustomerContextInterface
{
    /**
     * @return StoreInterface|boolean
     */
    public function getCustomer();
}
