<?php

namespace CoreShop\Component\Store\Model;

interface StoreAwareInterface
{
    /**
     * @return StoreInterface
     */
    public function getStore();

    /**
     * @param StoreInterface $store
     */
    public function setStore($store);
}