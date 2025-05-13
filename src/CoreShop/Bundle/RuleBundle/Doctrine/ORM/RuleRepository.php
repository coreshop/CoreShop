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
            ->getResult()
        ;
    }

    public function findWithConditionOfType($conditionType): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.conditions', 'condition')
            ->andWhere('condition.type = :conditionType')
            ->setParameter('conditionType', $conditionType)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWithActionOfType($actionType): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.actions', 'action')
            ->andWhere('action.type = :actionType')
            ->setParameter('actionType', $actionType)
            ->getQuery()
            ->getResult()
        ;
    }
}
