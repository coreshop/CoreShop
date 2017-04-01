<?php

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;

final class ORMRepositoryClassSubscriber extends AbstractDoctrineSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $this->setCustomRepositoryClass($eventArgs->getClassMetadata());
    }

    /**
     * @param ClassMetadata $metadata
     */
    private function setCustomRepositoryClass(ClassMetadata $metadata)
    {
        try {
            $resourceMetadata = $this->resourceRegistry->getByClass($metadata->getName());
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if ($resourceMetadata->hasClass('repository')) {
            $metadata->setCustomRepositoryClass($resourceMetadata->getClass('repository'));
        }
    }
}
