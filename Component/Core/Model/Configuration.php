<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Configuration\Model\Configuration as BaseConfiguration;

class Configuration extends BaseConfiguration implements ConfigurationInterface
{
    /**
     * @var StoreInterface
     */
    protected $store;

    /**
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param StoreInterface $store
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;
    }
}
