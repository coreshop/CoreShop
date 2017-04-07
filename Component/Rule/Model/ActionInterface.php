<?php

namespace CoreShop\Component\Rule\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ActionInterface extends ResourceInterface
{
    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getConfiguration();
}
