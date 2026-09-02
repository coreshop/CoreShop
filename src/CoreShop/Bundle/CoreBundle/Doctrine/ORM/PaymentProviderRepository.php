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

use CoreShop\Bundle\PaymentBundle\Doctrine\ORM\PaymentProviderRepository as BasePaymentProviderRepository;
use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class PaymentProviderRepository extends BasePaymentProviderRepository implements PaymentProviderRepositoryInterface
{
    public function findActiveForStore(StoreInterface $store): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.stores', 's')
            ->andWhere('o.active = true')
            ->andWhere('s.id = :storeId')
            ->addOrderBy('o.position')
            ->setParameter('storeId', $store->getId())
            ->getQuery()
            ->getResult()
        ;
    }
}
