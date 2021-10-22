<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Construction\DoctrineObjectConstructor;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\ObjectConstructionException;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

class VersionObjectConstructor implements ObjectConstructorInterface
{
    public function __construct(private ObjectConstructorInterface $fallbackConstructor, private ObjectConstructorInterface $fallbacksFallbackConstructor, private string $fallbackStrategy = DoctrineObjectConstructor::ON_MISSING_NULL, private ?\JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy $expressionLanguageExclusionStrategy = null)
    {
    }

    public function construct(DeserializationVisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context): ?object
    {
        if (!$context->hasAttribute('em')) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        $objectManager = $context->getAttribute('em');

        if (!$objectManager) {
            // No ObjectManager found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Locate possible ClassMetadata
        $classMetadataFactory = $objectManager->getMetadataFactory();

        if ($classMetadataFactory->isTransient($metadata->name)) {
            // No ClassMetadata found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Managed entity, check for proxy load
        if (!\is_array($data) && !(is_object($data) && \SimpleXMLElement::class === $data::class)) {
            // Single identifier, load proxy
            return $objectManager->getReference($metadata->name, $data);
        }

        // Fallback to default constructor if missing identifier(s)
        $classMetadata = $objectManager->getClassMetadata($metadata->name);
        $identifierList = [];

        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
            // Avoid calling objectManager->find if some identification properties are excluded
            if (!isset($metadata->propertyMetadata[$name])) {
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }

            /**
             * @var PropertyMetadata $propertyMetadata
             */
            $propertyMetadata = $metadata->propertyMetadata[$name];

            // Avoid calling objectManager->find if some identification properties are excluded by some exclusion strategy
            if ($this->isIdentifierFieldExcluded($propertyMetadata, $context)) {
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }

            if (!array_key_exists($propertyMetadata->serializedName, $data)) {
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }

            $identifierList[$name] = $data[$propertyMetadata->serializedName];
        }

        if (empty($identifierList)) {
            // $classMetadataFactory->isTransient() fails on embeddable class with file metadata driver
            // https://github.com/doctrine/persistence/issues/37
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        if (array_key_exists('id', $identifierList) && !$identifierList['id']) {
            return $this->fallbacksFallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Entity update, load it from database
        $object = $objectManager->find($metadata->name, $identifierList);

        if (null === $object) {
            switch ($this->fallbackStrategy) {
                case DoctrineObjectConstructor::ON_MISSING_NULL:
                    return null;

                case DoctrineObjectConstructor::ON_MISSING_EXCEPTION:
                    throw new ObjectConstructionException(sprintf('Entity %s can not be found', $metadata->name));

                case DoctrineObjectConstructor::ON_MISSING_FALLBACK:
                    return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);

                default:
                    throw new InvalidArgumentException('The provided fallback strategy for the object constructor is not valid');
            }
        }

        $objectManager->initializeObject($object);

        return $object;
    }

    private function isIdentifierFieldExcluded(PropertyMetadata $propertyMetadata, DeserializationContext $context): bool
    {
        $exclusionStrategy = $context->getExclusionStrategy();
        /** @psalm-suppress InternalMethod */
        if (null !== $exclusionStrategy && $exclusionStrategy->shouldSkipProperty($propertyMetadata, $context)) {
            return true;
        }

        /** @psalm-suppress InternalMethod */
        return null !== $this->expressionLanguageExclusionStrategy && $this->expressionLanguageExclusionStrategy->shouldSkipProperty($propertyMetadata, $context);
    }
}
