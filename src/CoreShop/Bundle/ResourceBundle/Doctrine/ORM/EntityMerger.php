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

namespace CoreShop\Bundle\ResourceBundle\Doctrine\ORM;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Utility\IdentifierFlattener;

class EntityMerger
{
    /**
     * The EntityManager that "owns" this UnitOfWork instance.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * The IdentifierFlattener used for manipulating identifiers
     *
     * @var \Doctrine\ORM\Utility\IdentifierFlattener
     */
    private $identifierFlattener;

    /**
     * @param EntityManagerInterface $em
     */
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

    private function doMerge($entity, array &$visited): void
    {
        $oid = spl_object_hash($entity);

        if (isset($visited[$oid])) {
            return;
        }

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
                }
                else {
                    $managedCopy = $this->em->find($class->rootEntityName, $flatId);
                }

                if (!$managedCopy) {
                    $this->cascadeMerge($entity, $visited);

                    $this->em->getUnitOfWork()->persist($entity);
                }
                else {
                    $this->checkAssociations($entity, $managedCopy, $visited);

                    $this->em->getUnitOfWork()->removeFromIdentityMap($managedCopy);
                    $this->em->getUnitOfWork()->registerManaged($entity, $id, $this->getData($managedCopy));
                }
            }
        }

        $visited[$oid] = $entity; // mark visited

        $this->cascadeMerge($entity, $visited);
    }

    private function checkAssociations($entity, $managedCopy, array &$visited)
    {
        $class = $this->em->getClassMetadata(get_class($entity));

        foreach ($class->associationMappings as $assoc) {
            $origData = $class->reflFields[$assoc['fieldName']]->getValue($managedCopy);
            $newData = $class->reflFields[$assoc['fieldName']]->getValue($entity);

            if (!$origData instanceof PersistentCollection) {
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

            $assocClass = $this->em->getClassMetadata($assoc['targetEntity']);

            foreach ($origData as $origDatum) {
                $found = false;
                $origId = $assocClass->getIdentifierValues($origDatum);

                foreach ($newData as $newDatum) {
                    $newId = $assocClass->getIdentifierValues($newDatum);

                    if ($newId === $origId) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $this->doMerge($origDatum, $visited);
                    $this->em->getUnitOfWork()->scheduleOrphanRemoval($origDatum);
                }
            }
        }
    }

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

    private function getData($entity)
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
                if ( ! $value instanceof Collection) {
                    $value = new ArrayCollection($value);
                }

                $assoc = $class->associationMappings[$name];

                // Inject PersistentCollection
                $value = new PersistentCollection(
                    $this->em, $this->em->getClassMetadata($assoc['targetEntity']), $value
                );
                $value->setOwner($entity, $assoc);
                $value->setDirty( ! $value->isEmpty());

                $class->reflFields[$name]->setValue($entity, $value);

                $actualData[$name] = $value;

                continue;
            }

            if (( ! $class->isIdentifier($name) || ! $class->isIdGeneratorIdentity()) && ($name !== $class->versionField)) {
                $actualData[$name] = $value;
            }
        }

        return $actualData;
    }
}
