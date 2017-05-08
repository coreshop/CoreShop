<?php

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Configuration\Repository\ConfigurationRepositoryInterface as BaseConfigurationRepositoryInterface;
use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface ConfigurationRepositoryInterface extends BaseConfigurationRepositoryInterface
{
    /**
     * @param string $key
     * @param StoreInterface $store
     * @return ConfigurationInterface[]
     */
    public function findForKeyAndStore($key, StoreInterface $store);
}
