<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use CoreShop\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class NewResourceFactory implements NewResourceFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory)
    {
        if (null === $method = $requestConfiguration->getFactoryMethod()) {
            return $factory->createNew();
        }

        $arguments = array_values($requestConfiguration->getFactoryArguments());

        return $factory->$method(...$arguments);
    }
}
