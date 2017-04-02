<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface NewResourceFactoryInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param FactoryInterface $factory
     *
     * @return ResourceInterface
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory);
}
