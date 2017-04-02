<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourcesResolver implements ResourcesResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $repositoryMethod = $requestConfiguration->getRepositoryMethod();
        if (null !== $repositoryMethod) {
            $arguments = array_values($requestConfiguration->getRepositoryArguments());

            return $repository->$repositoryMethod(...$arguments);
        }

        $criteria = [];
        if ($requestConfiguration->isFilterable()) {
            $criteria = $requestConfiguration->getCriteria();
        }

        $sorting = [];
        if ($requestConfiguration->isSortable()) {
            $sorting = $requestConfiguration->getSorting();
        }

        if ($requestConfiguration->isPaginated()) {
            return $repository->createPaginator($criteria, $sorting);
        }

        return $repository->findBy($criteria, $sorting, $requestConfiguration->getLimit());
    }
}
