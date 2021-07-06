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

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Doctrine\ORM;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Utility\IdentifierFlattener;

class EntityMerger
{
    private EntityManagerInterface $em;
    private IdentifierFlattener $identifierFlattener;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->identifierFlattener = new IdentifierFlattener($em->getUnitOfWork(), $em->getMetadataFactory());
    }

    /**
     * @param ResourceInterface $entity
     */
    public function merge(ResourceInterface $entity): void
    {
        $visited = [];

        $this->doMerge($entity, $visited);
    }

    /**
     * @param mixed $entity
     * @param array $visited
     */
    private function doMerge($entity, array &$visited): void
    {
        $oid = spl_object_hash($entity);

        if (isset($visited[$oid])) {
            return;
        }

        $visited[$oid] = $entity; // mark visited

        if ($entity instanceof Proxy && !$entity->__isInitialized()) {
            $entity->__load();
        }

        $class = $this->em->getClassMetadata(get_class($entity));

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
                    $this->checkAssociations($entity, $managedCopy, $visited);
                    $this->em->getUnitOfWork()->removeFromIdentityMap($managedCopy);
                    $this->em->getUnitOfWork()->registerManaged($entity, $id, $this->getData($managedCopy));
                }
            }
        }

        $this->cascadeMerge($entity, $visited);
    }

    /**
     * @param mixed $entity
     * @param mixed $managedCopy
     * @param array $visited
     */
    private function checkAssociations($entity, $managedCopy, array &$visited): void
    {
        $class = $this->em->getClassMetadata(get_class($entity));

        foreach ($class->associationMappings as $assoc) {
            $origData = $class->reflFields[$assoc['fieldName']]->getValue($managedCopy);
            $newData = $class->reflFields[$assoc['fieldName']]->getValue($entity);

            if (!$origData instanceof PersistentCollection) {
                continue;
            }

            if (!$origData->isInitialized()) {
                $origData->initialize();
            }

            if ($assoc['type'] === ClassMetadata::MANY_TO_MANY) {
                $newCollection = $origData;

                //Reset new Data, for some reason the line above resets newData
                $newData = $class->reflFields[$assoc['fieldName']]->getValue($entity);

                if (!$newCollection instanceof PersistentCollection) {
                    $newCollection = new PersistentCollection(
                        $this->em,
                        $class,
                        $newCollection
                    );
                }

                $this->mergeCollection($origData, $newData, $assoc, static function ($foundEntry) use ($newCollection) {
                    $newCollection->removeElement($foundEntry);
                }, $visited);

                $this->mergeCollection($newData, $origData, $assoc, static function ($foundEntry) use ($newCollection) {
                    $newCollection->add($foundEntry);
                }, $visited);

                // @todo: check if we really need to build this reference!
                $newData = &$newCollection;

                $newCollection->setOwner($entity, $assoc);
                $class->reflFields[$assoc['fieldName']]->setValue($entity, $newCollection);

                continue;
            }

            if (!($assoc['type'] & ClassMetadata::TO_MANY &&
                $assoc['orphanRemoval'] &&
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

            $this->mergeCollection($origData, $newData, $assoc, function ($foundEntry) {
                $this->em->getUnitOfWork()->scheduleOrphanRemoval($foundEntry);
            }, $visited);
        }
    }

    /**
     * @param Collection $from
     * @param Collection $to
     * @param array      $assoc
     * @param \Closure   $notFound
     * @param array      $visited
     */
    private function mergeCollection(Collection $from, Collection $to, array $assoc, \Closure $notFound, array &$visited): void
    {
        $assocClass = $this->em->getClassMetadata($assoc['targetEntity']);

        foreach ($from as $fromEntry) {
            $found = false;
            $origId = $assocClass->getIdentifierValues($fromEntry);

            foreach ($to as $toEntry) {
                $newId = $assocClass->getIdentifierValues($toEntry);

                if ($newId === $origId) {
                    $found = true;
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
     * @param array $visited
     */
    private function cascadeMerge($entity, array &$visited): void
    {
        $class = $this->em->getClassMetadata(get_class($entity));

        foreach ($class->associationMappings as $assoc) {
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
     *
     * @return array
     */
    private function getData($entity): array
    {
        $actualData = [];
        $class = $this->em->getClassMetadata(get_class($entity));

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
