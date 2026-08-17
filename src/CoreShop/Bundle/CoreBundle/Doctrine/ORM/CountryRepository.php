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

use CoreShop\Bundle\AddressBundle\Doctrine\ORM\CountryRepository as BaseCountryRepository;
use CoreShop\Component\Core\Repository\CountryRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class CountryRepository extends BaseCountryRepository implements CountryRepositoryInterface
{
    public function findForStore(StoreInterface $store): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.stores', 's')
            ->andWhere('o.active = true')
            ->andWhere('o.id = :storeId')
            ->setParameter('storeId', $store->getId())
            ->getQuery()
            ->getResult()
        ;
    }
}
