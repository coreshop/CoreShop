<?php

namespace CoreShop\Component\Configuration\Service;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;

interface ConfigurationServiceInterface
{
    /**
     * @param $key
     * @param boolean $returnObject
     * @return ConfigurationInterface
     */
    public function get($key, $returnObject = false);

    /**
     * @param $key
     * @param $data
     * @return ConfigurationInterface
     */
    public function set($key, $data);

    /**
     * @param $key
     * @return ConfigurationInterface
     */
    public function remove($key);
}