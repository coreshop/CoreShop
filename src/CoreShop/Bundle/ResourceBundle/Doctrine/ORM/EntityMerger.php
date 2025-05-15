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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Doctrine\ORM;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Utility\IdentifierFlattener;
use Doctrine\Persistence\Proxy;

class EntityMerger
{
    private IdentifierFlattener $identifierFlattener;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        /**
         * @psalm-suppress InvalidArgument
         */
        $this->identifierFlattener = new IdentifierFlattener($em->getUnitOfWork(), $em->getMetadataFactory());
    }

    public function merge(ResourceInterface $entity): void
    {
        $visited = [];

        $this->doMerge($entity, $visited);
    }

    /**
     * @param mixed $entity
     */
    private function doMerge($entity, array &$visited): void
    {
        $oid = spl_object_hash($entity);

        if (isset($visited[$oid])) {
            return;
        }

        $visited[$oid] = $entity; // mark visited
        $class = $this->em->getClassMetadata($entity::class);
        $entity = $this->loadEntity($entity);

        if ($this->em->getUnitOfWork()->getEntityState($entity, UnitOfWork::STATE_DETACHED) !== UnitOfWork::STATE_MANAGED) {
            $id = $class->getIdentifierValues($entity);

            // If there is no ID, it is actually NEW.
            if (!$id) {
                $this->cascadeMerge($entity, $visited);

                $this->em->persist($entity);
            } else {
                $flatId = ($class->containsForeignIdentifier)
                    ? $this->identifierFlattener->flattenIdentifier($class, $id)
                    : $id;

                $managedCopy = $this->em->getUnitOfWork()->tryGetById($flatId, $class->rootEntityName);

                if ($managedCopy) {
                    $visited[spl_object_hash($managedCopy)] = $managedCopy;
                } else {
                    $managedCopy = $this->em->find($class->rootEntityName, $flatId);
                }

                if (!$managedCopy) {
                    $this->cascadeMerge($entity, $visited);
                    $this->em->getUnitOfWork()->persist($entity);
                } else {
                    $managedCopy = $this->loadEntity($managedCopy);

                    $this->checkAssociations($entity, $managedCopy, $visited);
                    $this->em->getUnitOfWork()->removeFromIdentityMap($managedCopy);
                    $this->em->getUnitOfWork()->registerManaged($entity, $id, $this->getData($managedCopy));
                }
            }
        }

        $this->cascadeMerge($entity, $visited);
    }

    protected function loadEntity($entity)
    {
        $class = $this->em->getClassMetadata($entity::class);

        if ($entity instanceof Proxy && !$entity->__isInitialized()) {
            $id = $class->getIdentifierValues($entity);
            $uwEntity = $this->em->getUnitOfWork()->tryGetById($id, $class->getName());

            if ($uwEntity) {
                $this->em->getUnitOfWork()->removeFromIdentityMap($uwEntity);
            }

            $entity->__load();
        }

        return $entity;
    }

    /**
     * @param object $entity
     * @param object $managedCopy
     */
    private function checkAssociations($entity, $managedCopy, array &$visited): void
    {
        $class = $this->em->getClassMetadata($entity::class);

        foreach ($class->associationMappings as $assoc) {
            /**
             * @psalm-suppress PossiblyFalseArgument
             */
            $origData = $class->reflFields[$assoc['fieldName']]->getValue($managedCopy);
            $newData = $class->reflFields[$assoc['fieldName']]->getValue($entity);

            if (!$origData instanceof PersistentCollection) {
                continue;
            }

            if (!$origData->isInitialized()) {
                $origData->initialize();
            }

            $reflectionClass = new \ReflectionClass($origData);
            $property = $reflectionClass->getProperty('collection');
            $property->setAccessible(true);
            $origDataCollection = $property->getValue($origData);

            if (!$origDataCollection instanceof Collection) {
                continue;
            }

            if ($newData instanceof PersistentCollection) {
                if (!$newData->isInitialized()) {
                    $newData->initialize();
                }

                $reflectionClass = new \ReflectionClass($newData);
                $property = $reflectionClass->getProperty('collection');
                $property->setAccessible(true);
                $newDataCollection = $property->getValue($newData);

                if (!$newDataCollection instanceof Collection) {
                    continue;
                }
            } else {
                $newDataCollection = $newData;
            }

            if ($assoc['type'] === ClassMetadata::MANY_TO_MANY) {
                $newCollection = new PersistentCollection(
                    $this->em,
                    $class,
                    $origDataCollection,
                );
                $newCollection->setOwner($entity, $assoc);
                $newCollection->takeSnapshot();

                $this->mergeCollection($origDataCollection, $newDataCollection, $assoc, static function (mixed $foundEntry) use ($newCollection): void {
                    $newCollection->removeElement($foundEntry);
                }, $visited);

                $this->mergeCollection($newDataCollection, $origDataCollection, $assoc, static function (mixed $foundEntry) use ($newCollection): void {
                    $found = false;

                    foreach ($newCollection as $entry) {
                        if (spl_object_hash($entry) === spl_object_hash($foundEntry)) {
                            $found = true;

                            break;
                        }
                    }

                    if (!$found) {
                        $newCollection->add($foundEntry);
                    }
                }, $visited, true);

                // @todo: check if we really need to build this reference!
                $newData = &$newCollection;

                if ($newData->isDirty() && count($newData->getSnapshot()) > 0) {
                    $snapshotEntries = [];
                    $assocClass = $this->em->getClassMetadata($assoc['targetEntity']);

                    foreach ($newData->getSnapshot() as $snapshotEntry) {
                        $origId = $assocClass->getIdentifierValues($snapshotEntry);
                        $flatId = ($assocClass->containsForeignIdentifier)
                            ? $this->identifierFlattener->flattenIdentifier($class, $origId)
                            : $origId;

                        $managedCopy = $this->em->getUnitOfWork()->tryGetById($flatId, $assocClass->rootEntityName);

                        if ($managedCopy) {
                            $snapshotEntries[] = $managedCopy;
                        } else {
                            $snapshotEntries[] = $snapshotEntry;
                        }
                    }

                    $reflectionClass = new \ReflectionClass($newData);
                    $property = $reflectionClass->getProperty('snapshot');
                    $property->setAccessible(true);
                    $property->setValue($newData, $snapshotEntries);
                }

                $class->reflFields[$assoc['fieldName']]->setValue($entity, $newCollection);

                continue;
            }

            if (!($assoc['type'] & ClassMetadata::TO_MANY &&
                isset($assoc['orphanRemoval']) && $assoc['orphanRemoval'] &&
                $origData->getOwner())) {
                continue;
            }

            if ($origData === $newData) {
                continue;
            }

            if (null === $newData) {
                foreach ($origData as $origDatum) {
                    $this->doMerge($origDatum, $visited);
                    $this->em->getUnitOfWork()->scheduleOrphanRemoval($origDatum);
                }

                continue;
            }

            $this->mergeCollection($origDataCollection, $newDataCollection, $assoc, function (mixed $foundEntry): void {
                $this->em->getUnitOfWork()->scheduleOrphanRemoval($foundEntry);
            }, $visited);
        }
    }

    private function mergeCollection(Collection $from, Collection $to, array $assoc, \Closure $notFound, array &$visited, bool $mergeFoundEntity = false): void
    {
        $assocClass = $this->em->getClassMetadata($assoc['targetEntity']);

        foreach ($from as $fromEntry) {
            $found = false;
            $origId = $assocClass->getIdentifierValues($fromEntry);

            foreach ($to as $offset => $toEntry) {
                $newId = $assocClass->getIdentifierValues($toEntry);

                if (!$newId) {
                    continue;
                }

                if ($newId === $origId) {
                    $found = true;

                    if ($mergeFoundEntity) {
                        $to->set($offset, $fromEntry);
                    }

                    break;
                }
            }

            if (!$found) {
                $this->doMerge($fromEntry, $visited);
                $notFound($fromEntry);
            }
        }
    }

    /**
     * @param mixed $entity
     */
    private function cascadeMerge($entity, array &$visited): void
    {
        $class = $this->em->getClassMetadata($entity::class);

        $associationMappings = array_filter(
            $class->associationMappings,
            static function ($assoc) {
                return $assoc['isCascadeMerge'];
            },
        );
        $noMergeAssociationMappings = array_filter(
            $class->associationMappings,
            static function ($assoc) {
                return !$assoc['isCascadeMerge'];
            },
        );

        /**
         * Restore managed entities to their original state
         */
        foreach ($noMergeAssociationMappings as $assoc) {
            $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);

            if (!$relatedEntities) {
                continue;
            }

            if ($relatedEntities instanceof Collection) {
                //Reset Collection
                $pColl = new PersistentCollection($this->em, $assoc['targetEntity'], new ArrayCollection());
                $pColl->setOwner($entity, $assoc);
                $pColl->setInitialized(false);

                $class->reflFields[$assoc['fieldName']]->setValue($entity, $pColl);
            } else {
                //Reset "tmp" entity with managed entity
                $relatedEntityClass = $this->em->getClassMetadata($assoc['targetEntity']);
                $id = $relatedEntityClass->getIdentifierValues($relatedEntities);

                if (!$id) {
                    continue;
                }

                $uwEntity = $this->em->getUnitOfWork()->tryGetById($id, $relatedEntityClass->getName());

                //Entity might not be loaded and managed yet, try to load it
                if (!$uwEntity) {
                    $uwEntity = $this->em->find($relatedEntityClass->getName(), $id);
                }

                if ($uwEntity) {
                    $class->reflFields[$assoc['fieldName']]->setValue($entity, $uwEntity);
                }
            }
        }

        /**
         * Merge related entities to the new state
         */
        foreach ($associationMappings as $assoc) {
            $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);

            if ($relatedEntities instanceof Collection) {
                if ($relatedEntities instanceof PersistentCollection) {
                    // Unwrap so that foreach() does not initialize
                    $relatedEntities = $relatedEntities->unwrap();
                }

                foreach ($relatedEntities as $relatedEntity) {
                    $this->doMerge($relatedEntity, $visited);
                }
            } else {
                if ($relatedEntities !== null) {
                    $this->doMerge($relatedEntities, $visited);
                }
            }
        }
    }

    /**
     * @param mixed $entity
     */
    private function getData($entity): array
    {
        $actualData = [];
        $class = $this->em->getClassMetadata($entity::class);

        foreach ($class->reflFields as $name => $refProp) {
            $value = $refProp->getValue($entity);

            if ($class->isCollectionValuedAssociation($name) && $value !== null) {
                if ($value instanceof PersistentCollection) {
                    if ($value->getOwner() === $entity) {
                        continue;
                    }

                    $value = new ArrayCollection($value->getValues());
                }

                // If $value is not a Collection then use an ArrayCollection.
                if (!$value instanceof Collection) {
                    $value = new ArrayCollection($value);
                }

                $assoc = $class->associationMappings[$name];

                // Inject PersistentCollection
                $value = new PersistentCollection($this->em, $this->em->getClassMetadata($assoc['targetEntity']), $value);
                $value->setOwner($entity, $assoc);
                $value->setDirty(!$value->isEmpty());

                $class->reflFields[$name]->setValue($entity, $value);

                $actualData[$name] = $value;

                continue;
            }

            if ((!$class->isIdentifier($name) || !$class->isIdGeneratorIdentity()) && ($name !== $class->versionField)) {
                $actualData[$name] = $value;
            }
        }

        return $actualData;
    }
}
