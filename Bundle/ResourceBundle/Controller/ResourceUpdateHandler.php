<?php

namespace CoreShop\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use CoreShop\Component\Resource\Model\ResourceInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ResourceUpdateHandler implements ResourceUpdateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(
        ResourceInterface $resource,
        RequestConfiguration $configuration,
        ObjectManager $manager
    ) {
        $manager->flush();
    }
}
