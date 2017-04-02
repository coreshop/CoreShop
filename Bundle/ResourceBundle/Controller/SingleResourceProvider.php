<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SingleResourceProvider implements SingleResourceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $repositoryMethod = $requestConfiguration->getRepositoryMethod();
        if (null !== $repositoryMethod) {
            $arguments = array_values($requestConfiguration->getRepositoryArguments());

            return $repository->$repositoryMethod(...$arguments);
        }

        $criteria = [];
        $request = $requestConfiguration->getRequest();

        if ($request->attributes->has('id')) {
            return $repository->find($request->attributes->get('id'));
        }

        if ($request->attributes->has('slug')) {
            $criteria = ['slug' => $request->attributes->get('slug')];
        }

        $criteria = array_merge($criteria, $requestConfiguration->getCriteria());

        return $repository->findOneBy($criteria);
    }
}
