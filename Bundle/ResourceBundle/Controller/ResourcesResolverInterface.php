<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ResourcesResolverInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
