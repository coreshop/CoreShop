<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

final class ResourcesCollectionProvider implements ResourcesCollectionProviderInterface
{
    /**
     * @var ResourcesResolverInterface
     */
    private $resourcesResolver;

    /**
     * @param ResourcesResolverInterface $resourcesResolver
     */
    public function __construct(ResourcesResolverInterface $resourcesResolver)
    {
        $this->resourcesResolver = $resourcesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $resources = $this->resourcesResolver->getResources($requestConfiguration, $repository);

        return $resources;
    }
}
