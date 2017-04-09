<?php

namespace CoreShop\Component\Store\Context;

use CoreShop\Component\Store\Model\StoreInterface;

interface StoreContextInterface
{
    /**
     * @return StoreInterface
     *
     * @throws StoreNotFoundException
     */
    public function getStore();
}
