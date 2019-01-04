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

namespace CoreShop\Bundle\RuleBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Rule\Repository\RuleRepositoryInterface;

class RuleRepository extends EntityRepository implements RuleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActive()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.active = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithConditionOfType($conditionType)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.conditions', 'condition')
            ->andWhere('condition.type = :conditionType')
            ->setParameter('conditionType', $conditionType)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithActionOfType($actionType)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.actions', 'action')
            ->andWhere('action.type = :actionType')
            ->setParameter('actionType', $actionType)
            ->getQuery()
            ->getResult();
    }
}
