<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\FixtureBundle\Repository;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class DataFixtureRepository extends EntityRepository implements DataFixtureRepositoryInterface
{
    public function findByClassName($className)
    {
        return $this->findBy(['className' => $className]);
    }

    public function isDataFixtureExists($where, array $parameters = [])
    {
        $entityId = $this->createQueryBuilder('m')
            ->select('m.id')
            ->where($where)
            ->setMaxResults(1)
            ->getQuery()
            ->execute($parameters);

        return $entityId ? true : false;
    }

    public function updateDataFixtureHistory(array $updateFields, $where, array $parameters = [])
    {
        $qb = $this->_em
            ->createQueryBuilder()
            ->update($this->getEntityName(), 'm')
            ->where($where);

        foreach ($updateFields as $fieldName => $fieldValue) {
            $qb->set($fieldName, $fieldValue);
        }
        $qb->getQuery()->execute($parameters);
    }
}
