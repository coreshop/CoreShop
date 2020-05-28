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

namespace CoreShop\Bundle\NotificationBundle\Doctrine\ORM;

use CoreShop\Bundle\RuleBundle\Doctrine\ORM\RuleRepository;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;

class NotificationRuleRepository extends RuleRepository implements NotificationRuleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForType($type): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.type = :type')
            ->andWhere('o.active = :active')
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }
}
