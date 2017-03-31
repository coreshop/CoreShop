<?php

namespace CoreShop\Bundle\ConfigurationBundle\Helper;

interface ConfigurationHelperInterface {

    /**
     * @return mixed
     */
    public function isMultiStoreEnabled();

    /**
     * @param $key
     * @param null $shopId
     * @param bool $returnObject
     * @return mixed
     */
    public function get($key, $shopId = null, $returnObject = false);

    /**
     * @param string $key
     * @param mixed $value
     * @param int $storeId
     * @return mixed
     */
    public function set($key, $value, $storeId = null);

    /**
     * @param $key
     * @return mixed
     */
    public function remove($key);

}