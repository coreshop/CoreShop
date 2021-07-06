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

namespace CoreShop\Bundle\FixtureBundle\Fixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class AbstractEntityReferenceFixture extends AbstractFixture
{
    protected function getObjectReferences(ObjectManager $objectManager, string $className): array
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

    protected function getObjectReferencesByIds(ObjectManager $objectManager, string $className, array $ids): array
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
