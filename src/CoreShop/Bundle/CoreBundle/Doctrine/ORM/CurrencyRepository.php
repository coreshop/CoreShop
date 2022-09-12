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

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\CurrencyBundle\Doctrine\ORM\CurrencyRepository as BaseCurrencyRepository;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class CurrencyRepository extends BaseCurrencyRepository implements CurrencyRepositoryInterface
{
    public function findActiveForStore(StoreInterface $store): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.countries', 'c')
            ->innerJoin('c.stores', 's')
            ->andWhere('c.active = true')
            ->andWhere('s.id = :storeId')
            ->setParameter('storeId', $store->getId())
            ->distinct()
            ->getQuery()
            ->getResult()
        ;
    }
}
