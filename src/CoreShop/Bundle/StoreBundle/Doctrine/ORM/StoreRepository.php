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

namespace CoreShop\Bundle\StoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Doctrine\ORM\QueryBuilder;

class StoreRepository extends EntityRepository implements StoreRepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o');
    }

    public function findOneBySite(int $siteId): ?StoreInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findStandard(): ?StoreInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isDefault = 1')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
