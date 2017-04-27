<?php

namespace CoreShop\Component\Configuration\Repository;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface ConfigurationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $key
     * @return ConfigurationInterface
     */
    public function findByKey($key);
}
