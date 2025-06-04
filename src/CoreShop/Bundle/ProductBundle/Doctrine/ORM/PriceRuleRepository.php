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

namespace CoreShop\Bundle\ProductBundle\Doctrine\ORM;

use CoreShop\Bundle\RuleBundle\Doctrine\ORM\RuleRepository;
use CoreShop\Component\Product\Repository\PriceRuleRepositoryInterface;

class PriceRuleRepository extends RuleRepository implements PriceRuleRepositoryInterface
{
    public function findActive(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.active = 1')
            ->addOrderBy('o.priority', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
