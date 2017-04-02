<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface ResourceUpdateHandlerInterface
{
    /**
     * @param ResourceInterface $resource
     * @param RequestConfiguration $requestConfiguration
     * @param ObjectManager $manager
     */
    public function handle(
        ResourceInterface $resource,
        RequestConfiguration $requestConfiguration,
        ObjectManager $manager
    );
}
