<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

final class ORMMappedSuperClassSubscriber extends AbstractDoctrineSubscriber
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
        $metadata = $eventArgs->getClassMetadata();

        $this->convertToEntityIfNeeded($metadata);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $eventArgs->getEntityManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function convertToEntityIfNeeded(ClassMetadataInfo $metadata)
    {
        if (false === $metadata->isMappedSuperclass) {
            return;
        }

        try {
            $resourceMetadata = $this->resourceRegistry->getByClass($metadata->getName());
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if ($metadata->getName() === $resourceMetadata->getClass('model')) {
            $metadata->isMappedSuperclass = false;
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param Configuration     $configuration
     */
    private function setAssociationMappings(ClassMetadataInfo $metadata, $configuration)
    {
        $class = $metadata->getName();
        if (!class_exists($class)) {
            return;
        }
        foreach (class_parents($class) as $parent) {
            if (false === in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                continue;
            }

            $parentMetadata = new ClassMetadata(
                $parent,
                $configuration->getNamingStrategy()
            );

            // Wakeup Reflection
            $parentMetadata->wakeupReflection($this->getReflectionService());

            // Load Metadata
            $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);

            if (false === $this->isResource($parentMetadata)) {
                continue;
            }

            if ($parentMetadata->isMappedSuperclass) {
                foreach ($parentMetadata->getAssociationMappings() as $key => $value) {
                    if ($this->isRelation($value['type']) && !isset($metadata->associationMappings[$key])) {
                        $metadata->associationMappings[$key] = $value;
                    }
                }
            }
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        if (false === $this->isResource($metadata)) {
            return;
        }

        foreach ($metadata->getAssociationMappings() as $key => $value) {
            if ($this->isRelation($value['type'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isRelation($type)
    {
        return in_array(
            $type,
            [
                ClassMetadataInfo::MANY_TO_MANY,
                ClassMetadataInfo::ONE_TO_MANY,
                ClassMetadataInfo::ONE_TO_ONE,
            ],
            true
        );
    }
}
