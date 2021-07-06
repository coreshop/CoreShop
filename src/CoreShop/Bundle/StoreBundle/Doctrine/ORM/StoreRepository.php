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
            ->getOneOrNullResult();
    }

    public function findStandard(): ?StoreInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isDefault = 1')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
