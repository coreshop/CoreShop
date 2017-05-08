<?php

namespace CoreShop\Component\Core\Configuration;

use CoreShop\Component\Configuration\Service\ConfigurationServiceInterface as BaseConfigurationServiceInterface;
use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Component\Core\Model\StoreInterface;

interface ConfigurationServiceInterface extends BaseConfigurationServiceInterface
{
    /**
     * @param $key
     * @param StoreInterface|null $store
     * @param bool $returnObject
     * @return ConfigurationInterface
     */
    public function getForStore($key, StoreInterface $store = null, $returnObject = false);

    /**
     * @param $key
     * @param $data
     * @param StoreInterface|null $store
     * @return ConfigurationInterface
     */
    public function setForStore($key, $data, StoreInterface $store = null);

    /**
     * @param $key
     * @param StoreInterface|null $store
     * @return ConfigurationInterface
     */
    public function removeForStore($key, StoreInterface $store = null);
}