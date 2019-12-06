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
use function GuzzleHttp\Promise\unwrap;

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
        $this->em = $em;
        $this->identifierFlattener = new IdentifierFlattener($em->getUnitOfWork(), $em->getMetadataFactory());
        $this->reflectionPropertiesGetter = new ReflectionPropertiesGetter(new RuntimeReflectionService());
    }

    public function merge(ResourceInterface $entity)
    {
        $visited = [];

        $this->doMerge($entity, $visited);
    }

    private function doMerge($entity, array &$visited, $prevManagedCopy = null, array $assoc = [])
    {
        $oid = spl_object_hash($entity);

        if (isset($visited[$oid])) {
            $managedCopy = $visited[$oid];

            return $managedCopy;
        }

        $class = $this->em->getClassMetadata(get_class($entity));

        if ($this->em->getUnitOfWork()->getEntityState($entity,
                UnitOfWork::STATE_DETACHED) !== UnitOfWork::STATE_MANAGED) {
            $id = $class->getIdentifierValues($entity);

            // If there is no ID, it is actually NEW.
            if (!$id) {
                $this->em->persist($entity);
            } else {
                $flatId = ($class->containsForeignIdentifier)
                    ? $this->identifierFlattener->flattenIdentifier($class, $id)
                    : $id;

                $managedCopy = $this->em->getUnitOfWork()->tryGetById($flatId, $class->rootEntityName);

                if ($managedCopy) {
                    $visited[spl_object_hash($managedCopy)] = $managedCopy;

                    $this->em->getUnitOfWork()->removeFromIdentityMap($managedCopy);
                }

                $this->em->getUnitOfWork()->registerManaged($entity, $id, []);
            }
        }

        $visited[$oid] = $entity; // mark visited

        $this->cascadeMerge($entity, $visited);
    }

    private function cascadeMerge($entity, array &$visited)
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
                    $this->doMerge($relatedEntity, $visited, $entity, $assoc);
                }
            } else {
                if ($relatedEntities !== null) {
                    $this->doMerge($relatedEntities, $visited, $entity, $assoc);
                }
            }
        }
    }
}
