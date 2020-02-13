<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\ObjectConstructionException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\VisitorInterface;

class VersionObjectConstructor implements ObjectConstructorInterface
{
    private $fallbackConstructor;
    private $fallbacksFallbackConstructor;

    public function __construct(ObjectConstructorInterface $fallbackConstructor, ObjectConstructorInterface $fallbacksFallbackConstructor)
    {
        $this->fallbackConstructor = $fallbackConstructor;
        $this->fallbacksFallbackConstructor = $fallbacksFallbackConstructor;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        if (!$context->hasAttribute('em')) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        $em = $context->getAttribute('em');

        if (!$em instanceof EntityManager) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Locate possible ClassMetadata
        $classMetadataFactory = $em->getMetadataFactory();

        if ($classMetadataFactory->isTransient($metadata->name)) {
            // No ClassMetadata found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Managed entity, check for proxy load
        if (!\is_array($data)) {
            // Single identifier, load proxy
            return $em->getReference($metadata->name, $data);
        }

        // Fallback to default constructor if missing identifier(s)
        $classMetadata = $em->getClassMetadata($metadata->name);
        $identifierList = array();

        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
            if ($visitor instanceof AbstractVisitor) {
                /** @var PropertyNamingStrategyInterface $namingStrategy */
                $namingStrategy = $visitor->getNamingStrategy();
                $dataName = $namingStrategy->translateName($metadata->propertyMetadata[$name]);
            } else {
                $dataName = $name;
            }

            if (!array_key_exists($dataName, $data)) {
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }

            $identifierList[$name] = $data[$dataName];
        }

        foreach ($identifierList as $i => $value) {
            if (null === $value) {
                return $this->fallbacksFallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }
        }

        // Entity update, load it from database
        $object = $em->find($metadata->name, $identifierList);

        if (null === $object) {
            return $this->fallbacksFallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        $em->initializeObject($object);

        return $object;
    }
}
