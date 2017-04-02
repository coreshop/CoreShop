<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface ResourcesCollectionProviderInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
