<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface SingleResourceProviderInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     *
     * @return ResourceInterface|null
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
