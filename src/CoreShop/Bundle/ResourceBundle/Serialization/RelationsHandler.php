<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class RelationsHandler
{
    public function __construct(
        private EntityManagerInterface $manager,
    ) {
    }

    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        $manager = $this->manager;

        if ($context->hasAttribute('em') && $context->getAttribute('em') instanceof EntityManagerInterface) {
            $manager = $context->getAttribute('em');
        }

        if (is_array($relation)) {
            return array_map(function (mixed $rel) use ($manager): mixed {
                return $this->getSingleEntityRelation($rel, $manager);
            }, $relation);
        }

        return $this->getSingleEntityRelation($relation, $manager);
    }

    /**
     * @return (T|null)[]|T|null
     *
     * @psalm-return T|list<T|null>|null
     */
    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context): array|T|null
    {
        $className = $type['params'][0]['name'] ?? null;

        $manager = $this->manager;

        if ($context->hasAttribute('em') && $context->getAttribute('em') instanceof EntityManagerInterface) {
            $manager = $context->getAttribute('em');
        }

        $metadata = $manager->getClassMetadata($className);

        if (!is_array($relation)) {
            return $this->findById($relation, $metadata, $manager);
        }

        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && array_key_exists($idName, $relation);
            }
        }

        if ($single) {
            return $this->findById($relation, $metadata, $manager);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = $this->findById($idSet, $metadata, $manager);
        }

        return $objects;
    }

    protected function getSingleEntityRelation($relation, EntityManagerInterface $entityManager)
    {
        $metadata = $entityManager->getClassMetadata($relation::class);

        $ids = $metadata->getIdentifierValues($relation);
        if (!$metadata->isIdentifierComposite) {
            $ids = array_shift($ids);
        }

        return $ids;
    }

    /**
     * @return null|object
     *
     * @psalm-return T|null
     */
    protected function findById(array $id, ClassMetadata $metadata, EntityManagerInterface $manager)
    {
        return $manager->find($metadata->getName(), $id);
    }
}
