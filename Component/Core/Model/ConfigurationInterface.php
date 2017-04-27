<?php

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Configuration\Model\ConfigurationInterface as BaseConfigurationInterface;

interface ConfigurationInterface extends BaseConfigurationInterface
{
    /**
     * @return StoreInterface
     */
    public function getStore();

    /**
     * @param StoreInterface $store
     */
    public function setStore(StoreInterface $store);
}
