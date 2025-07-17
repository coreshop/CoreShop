<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Webmozart\Assert\Assert;

final class ORMMappedSuperClassSubscriber extends AbstractDoctrineSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $eventArgs->getEntityManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    private function setAssociationMappings(ClassMetadata $metadata, Configuration $configuration): void
    {
        $class = $metadata->getName();
        if (!class_exists($class)) {
            return;
        }

        /** @psalm-suppress DeprecatedClass */
        $metadataDriver = $configuration->getMetadataDriverImpl();
        Assert::isInstanceOf($metadataDriver, MappingDriver::class);

        $parents = class_parents($class) ?: [];

        foreach ($parents as $parent) {
            if (false === in_array($parent, $metadataDriver->getAllClassNames(), true)) {
                continue;
            }

            $parentMetadata = new ClassMetadata(
                $parent,
                $configuration->getNamingStrategy(),
            );

            // Wakeup Reflection
            /** @psalm-suppress ArgumentTypeCoercion */
            $parentMetadata->wakeupReflection($this->getReflectionService());

            // Load Metadata
            $metadataDriver->loadMetadataForClass($parent, $parentMetadata);

            /** @psalm-suppress InvalidArgument */
            if (false === $this->isResource($parentMetadata)) {
                continue;
            }

            if ($parentMetadata->isMappedSuperclass) {
                /**
                 * @var AssociationMapping|array{type: int} $value
                 */
                foreach ($parentMetadata->getAssociationMappings() as $key => $value) {
                    $type = \is_array($value) ? $value['type'] : $value->type();
                    if ($this->isRelation($type) && !isset($metadata->associationMappings[$key])) {
                        /**
                         * @psalm-suppress InvalidPropertyAssignmentValue
                         */
                        $metadata->associationMappings[$key] = $value; /** @phpstan-ignore-line */
                    }
                }
            }
        }
    }

    private function unsetAssociationMappings(ClassMetadata $metadata): void
    {
        /** @psalm-suppress InvalidArgument */
        if (false === $this->isResource($metadata)) {
            return;
        }

        /**
         * @var AssociationMapping|array{type: int} $value
         */
        foreach ($metadata->getAssociationMappings() as $key => $value) {
            $type = \is_array($value) ? $value['type'] : $value->type();
            if ($this->isRelation($type)) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }

    private function isRelation(int $type): bool
    {
        return in_array(
            $type,
            [
                ClassMetadata::MANY_TO_MANY,
                ClassMetadata::ONE_TO_MANY,
                ClassMetadata::ONE_TO_ONE,
            ],
            true,
        );
    }
}
