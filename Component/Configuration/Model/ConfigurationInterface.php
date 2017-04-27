<?php

namespace CoreShop\Component\Configuration\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ConfigurationInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param mixed $data
     */
    public function setData($data);

}