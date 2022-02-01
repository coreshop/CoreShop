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

namespace CoreShop\Bundle\FixtureBundle\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class AbstractEntityReferenceFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * Returns array of object references.
     *
     * @param ObjectManager $objectManager
     * @param string        $className
     *
     * @return array
     *
     * @see getObjectReferencesByIds
     */
    protected function getObjectReferences(ObjectManager $objectManager, $className)
    {
        $identifier = $objectManager->getClassMetadata($className)->getIdentifier();
        $idField = reset($identifier);

        /** @var EntityRepository $objectRepository */
        $objectRepository = $objectManager->getRepository($className);

        $idsResult = $objectRepository
            ->createQueryBuilder('t')
            ->select('t.' . $idField)
            ->getQuery()
            ->getArrayResult();

        $ids = [];
        foreach ($idsResult as $result) {
            $ids[] = $result[$idField];
        }

        return $this->getObjectReferencesByIds($objectManager, $className, $ids);
    }

    /**
     * Returns array of object references by their ids. It's useful when ids are known and objects are used as
     * other entities' relation.
     *
     * @param ObjectManager $objectManager
     * @param string        $className
     * @param array         $ids
     *
     * @return array
     */
    protected function getObjectReferencesByIds(ObjectManager $objectManager, $className, array $ids)
    {
        $entities = [];

        foreach ($ids as $id) {
            if ($objectManager instanceof EntityManager) {
                $entities[] = $objectManager->getReference($className, $id);
            }
        }

        return $entities;
    }
}
