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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Doctrine\ORM;

use CoreShop\Bundle\RuleBundle\Doctrine\ORM\RuleRepository;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;

class CartPriceRuleRepository extends RuleRepository implements CartPriceRuleRepositoryInterface
{
    public function findNonVoucherRules(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isVoucherRule = :isVoucherRule')
            ->setParameter('isVoucherRule', false)
            ->orderBy('o.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
