<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
