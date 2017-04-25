<?php

namespace CoreShop\Component\Store\Context;

use CoreShop\Component\Store\Model\StoreInterface;

final class CachedStoreContext implements StoreContextInterface
{
    /**
     * @var StoreInterface
     */
    private $store = null;

    /**
     * @param StoreInterface $store
     */
    public function setStore($store)
    {
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        if ($this->store instanceof StoreInterface) {
            return $this->store;
        }

        throw new StoreNotFoundException();
    }
}
