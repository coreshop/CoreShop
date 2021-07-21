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

namespace CoreShop\Bundle\RuleBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Rule\Repository\RuleRepositoryInterface;

class RuleRepository extends EntityRepository implements RuleRepositoryInterface
{
    public function findActive(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.active = 1')
            ->getQuery()
            ->getResult();
    }

    public function findWithConditionOfType($conditionType): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.conditions', 'condition')
            ->andWhere('condition.type = :conditionType')
            ->setParameter('conditionType', $conditionType)
            ->getQuery()
            ->getResult();
    }

    public function findWithActionOfType($actionType): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.actions', 'action')
            ->andWhere('action.type = :actionType')
            ->setParameter('actionType', $actionType)
            ->getQuery()
            ->getResult();
    }
}
