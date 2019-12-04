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
use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Reflection\ReflectionPropertiesGetter;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\ORM\TransactionRequiredException;
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
     * @var ReflectionPropertiesGetter
     */
    private $reflectionPropertiesGetter;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em                         = $em;
        $this->identifierFlattener        = new IdentifierFlattener($em->getUnitOfWork(), $em->getMetadataFactory());
        $this->reflectionPropertiesGetter = new ReflectionPropertiesGetter(new RuntimeReflectionService());
    }

    public function merge(ResourceInterface $entity)
    {
        $visited = [];

        return $this->doMerge($entity, $visited);
    }

    private function doMerge(ResourceInterface $entity, array &$visited, $prevManagedCopy = null, array $assoc = [])
    {
        $oid = spl_object_hash($entity);

        if (isset($visited[$oid])) {
            $managedCopy = $visited[$oid];

            if ($prevManagedCopy !== null) {
                $this->updateAssociationWithMergedEntity($entity, $assoc, $prevManagedCopy, $managedCopy);
            }

            return $managedCopy;
        }

        $class = $this->em->getClassMetadata(get_class($entity));

        // First we assume DETACHED, although it can still be NEW but we can avoid
        // an extra db-roundtrip this way. If it is not MANAGED but has an identity,
        // we need to fetch it from the db anyway in order to merge.
        // MANAGED entities are ignored by the merge operation.
        $managedCopy = $entity;
        $originalEntity = clone $entity;

        if ($this->em->getUnitOfWork()->getEntityState($entity, UnitOfWork::STATE_DETACHED) !== UnitOfWork::STATE_MANAGED) {
            // Try to look the entity up in the identity map.
            $id = $class->getIdentifierValues($entity);

            // If there is no ID, it is actually NEW.
            if ( ! $id) {
                //We can't create a new entity, we have to use the existing and merge it into the EM
                //$managedCopy = $this->newInstance($class);

                $this->mergeEntityStateIntoManagedCopy($originalEntity, $managedCopy);
                $this->em->persist($managedCopy);
            } else {
                $flatId = ($class->containsForeignIdentifier)
                    ? $this->identifierFlattener->flattenIdentifier($class, $id)
                    : $id;

                $managedCopy = $this->em->getUnitOfWork()->tryGetById($flatId, $class->rootEntityName);

                if ($managedCopy) {
                    // We have the entity in-memory already, just make sure its not removed.
                    if ($this->getEntityState($managedCopy) === UnitOfWork::STATE_REMOVED) {
                        throw ORMInvalidArgumentException::entityIsRemoved($managedCopy, "merge");
                    }
                } else {
                    // We need to fetch the managed copy in order to merge.
                    $managedCopy = $this->em->find($class->name, $flatId);
                }

                if ($managedCopy === null) {
                    // If the identifier is ASSIGNED, it is NEW, otherwise an error
                    // since the managed entity was not found.
                    if ( ! $class->isIdentifierNatural()) {
                        throw EntityNotFoundException::fromClassNameAndIdentifier(
                            $class->getName(),
                            $this->identifierFlattener->flattenIdentifier($class, $id)
                        );
                    }

                    //We can't create a new entity, we have to use the existing and merge it into the EM
                    //$managedCopy = $this->newInstance($class);
                    $class->setIdentifierValues($managedCopy, $id);

                    $this->mergeEntityStateIntoManagedCopy($originalEntity, $managedCopy);
                    $this->em->persist($managedCopy);
                } else {
                    $this->ensureVersionMatch($class, $originalEntity, $managedCopy);
                    $this->mergeEntityStateIntoManagedCopy($originalEntity, $managedCopy);
                }
            }

            $visited[$oid] = $managedCopy; // mark visited

            if ($class->isChangeTrackingDeferredExplicit()) {
                //TODO: HOW
                //$this->scheduleForDirtyCheck($originalEntity);
            }
        }

        if ($prevManagedCopy !== null) {
            $this->updateAssociationWithMergedEntity($originalEntity, $assoc, $prevManagedCopy, $managedCopy);
        }

        // Mark the managed copy visited as well
        $visited[spl_object_hash($managedCopy)] = $managedCopy;

        $this->cascadeMerge($originalEntity, $managedCopy, $visited);

        return $managedCopy;
    }

    /**
     * Cascades a merge operation to associated entities.
     *
     * @param object $entity
     * @param object $managedCopy
     * @param array  $visited
     *
     * @return void
     */
    private function cascadeMerge($entity, $managedCopy, array &$visited)
    {
        $class = $this->em->getClassMetadata(get_class($entity));

        $associationMappings = array_filter(
            $class->associationMappings,
            function ($assoc) { return $assoc['isCascadeMerge']; }
        );

        foreach ($associationMappings as $assoc) {
            $relatedEntities = $class->reflFields[$assoc['fieldName']]->getValue($entity);

            if ($relatedEntities instanceof Collection) {
                if ($relatedEntities === $class->reflFields[$assoc['fieldName']]->getValue($managedCopy)) {
                    continue;
                }

                if ($relatedEntities instanceof PersistentCollection) {
                    // Unwrap so that foreach() does not initialize
                    $relatedEntities = $relatedEntities->unwrap();
                }

                foreach ($relatedEntities as $relatedEntity) {
                    $this->doMerge($relatedEntity, $visited, $managedCopy, $assoc);
                }
            } else if ($relatedEntities !== null) {
                $this->doMerge($relatedEntities, $visited, $managedCopy, $assoc);
            }
        }
    }

    /**
     * @param object $entity
     * @param object $managedCopy
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function mergeEntityStateIntoManagedCopy($entity, $managedCopy)
    {
        if (! $this->isLoaded($entity)) {
            return;
        }

        if (! $this->isLoaded($managedCopy)) {
            $managedCopy->__load();
        }

        $class = $this->em->getClassMetadata(get_class($entity));

        foreach ($this->reflectionPropertiesGetter->getProperties($class->name) as $prop) {
            $name = $prop->name;

            $prop->setAccessible(true);

            if ( ! isset($class->associationMappings[$name])) {
                if ( ! $class->isIdentifier($name)) {
                    $prop->setValue($managedCopy, $prop->getValue($entity));
                }
            } else {
                $assoc2 = $class->associationMappings[$name];

                if ($assoc2['type'] & ClassMetadata::TO_ONE) {
                    $other = $prop->getValue($entity);
                    if ($other === null) {
                        $prop->setValue($managedCopy, null);
                    } else {
                        if ($other instanceof Proxy && !$other->__isInitialized()) {
                            // do not merge fields marked lazy that have not been fetched.
                            continue;
                        }

                        if ( ! $assoc2['isCascadeMerge']) {
                            if ($this->getEntityState($other) === UnitOfWork::STATE_DETACHED) {
                                $targetClass = $this->em->getClassMetadata($assoc2['targetEntity']);
                                $relatedId   = $targetClass->getIdentifierValues($other);

                                if ($targetClass->subClasses) {
                                    $other = $this->em->find($targetClass->name, $relatedId);
                                } else {
                                    $other = $this->em->getProxyFactory()->getProxy(
                                        $assoc2['targetEntity'],
                                        $relatedId
                                    );
                                    //TODO: HOW
                                    //$this->registerManaged($other, $relatedId, []);
                                }
                            }

                            $prop->setValue($managedCopy, $other);
                        }
                    }
                } else {
                    $mergeCol = $prop->getValue($entity);

                    if ($mergeCol instanceof PersistentCollection && ! $mergeCol->isInitialized()) {
                        // do not merge fields marked lazy that have not been fetched.
                        // keep the lazy persistent collection of the managed copy.
                        continue;
                    }

                    $managedCol = $prop->getValue($managedCopy);

                    if ( ! $managedCol) {
                        $managedCol = new PersistentCollection(
                            $this->em,
                            $this->em->getClassMetadata($assoc2['targetEntity']),
                            new ArrayCollection
                        );
                        $managedCol->setOwner($managedCopy, $assoc2);
                        $prop->setValue($managedCopy, $managedCol);
                    }

                    if ($managedCol instanceof ArrayCollection) {
                        $managedCol = new PersistentCollection(
                            $this->em,
                            $this->em->getClassMetadata($assoc2['targetEntity']),
                            $managedCol
                        );
                        $managedCol->setOwner($managedCopy, $assoc2);
                        $prop->setValue($managedCopy, $managedCol);
                    }

                    if ($assoc2['isCascadeMerge']) {
                        $managedCol->initialize();

                        // clear and set dirty a managed collection if its not also the same collection to merge from.
                        if ( ! $managedCol->isEmpty() && $managedCol !== $mergeCol) {
                            $managedCol->unwrap()->clear();
                            $managedCol->setDirty(true);

                            if ($assoc2['isOwningSide']
                                && $assoc2['type'] == ClassMetadata::MANY_TO_MANY
                                && $class->isChangeTrackingNotify()
                            ) {
                                //TODO: HOW?
                                //$this->scheduleForDirtyCheck($managedCopy);
                            }
                        }
                    }
                }
            }

            if ($class->isChangeTrackingNotify()) {
                // Just treat all properties as changed, there is no other choice.
                $this->em->getUnitOfWork()->propertyChanged($managedCopy, $name, null, $prop->getValue($managedCopy));
            }
        }
    }

    private function getEntityState($entity, $assume = null)
    {
        return $this->em->getUnitOfWork()->getEntityState($entity, $assume);
    }

    private function updateAssociationWithMergedEntity(ResourceInterface $entity, array $association, $previousManagedCopy, $managedCopy)
    {
        $assocField = $association['fieldName'];
        $prevClass  = $this->em->getClassMetadata(get_class($previousManagedCopy));

        if ($association['type'] & ClassMetadata::TO_ONE) {
            $prevClass->reflFields[$assocField]->setValue($previousManagedCopy, $managedCopy);

            return;
        }

        $value   = $prevClass->reflFields[$assocField]->getValue($previousManagedCopy);
        $value[] = $managedCopy;

        if ($association['type'] === ClassMetadata::ONE_TO_MANY) {
            $class = $this->em->getClassMetadata(get_class($entity));

            $class->reflFields[$association['mappedBy']]->setValue($managedCopy, $previousManagedCopy);
        }
    }

    /**
     * @param ClassMetadata $class
     *
     * @return \Doctrine\Common\Persistence\ObjectManagerAware|object
     */
    private function newInstance($class)
    {
        $entity = $class->newInstance();

        if ($entity instanceof \Doctrine\Common\Persistence\ObjectManagerAware) {
            $entity->injectObjectManager($this->em, $class);
        }

        return $entity;
    }

    /**
     * Tests if an entity is loaded - must either be a loaded proxy or not a proxy
     *
     * @param object $entity
     *
     * @return bool
     */
    private function isLoaded($entity)
    {
        return !($entity instanceof Proxy) || $entity->__isInitialized();
    }

    private function ensureVersionMatch(ClassMetadata $class, $entity, $managedCopy)
    {
        if (! ($class->isVersioned && $this->isLoaded($managedCopy) && $this->isLoaded($entity))) {
            return;
        }

        $reflField          = $class->reflFields[$class->versionField];
        $managedCopyVersion = $reflField->getValue($managedCopy);
        $entityVersion      = $reflField->getValue($entity);

        // Throw exception if versions don't match.
        if ($managedCopyVersion === $entityVersion) {
            return;
        }

        throw OptimisticLockException::lockFailedVersionMismatch($entity, $entityVersion, $managedCopyVersion);
    }
}
