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

namespace CoreShop\Bundle\NotificationBundle\Doctrine\ORM;

use CoreShop\Bundle\RuleBundle\Doctrine\ORM\RuleRepository;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;

class NotificationRuleRepository extends RuleRepository implements NotificationRuleRepositoryInterface
{
    public function findForType($type): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.type = :type')
            ->andWhere('o.active = :active')
            ->setParameter('type', $type)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult()
        ;
    }
}
