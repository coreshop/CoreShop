<?php

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Configuration\Repository\ConfigurationRepositoryInterface as BaseConfigurationRepositoryInterface;

interface ConfigurationRepositoryInterface extends BaseConfigurationRepositoryInterface
{
    /**
     * @param string $key
     * @param StoreInterface $store
     * @return ConfigurationInterface[]
     */
    public function findForKeyAndStore($key, StoreInterface $store);
}
