<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\PaymentBundle\Doctrine\ORM\PaymentProviderRepository as BasePaymentProviderRepository;
use CoreShop\Component\Core\Repository\PaymentProviderRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class PaymentProviderRepository extends BasePaymentProviderRepository implements PaymentProviderRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActiveForStore(StoreInterface $store)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.stores', 's')
            ->andWhere('o.active = true')
            ->andWhere('s.id = :storeId')
            ->addOrderBy('o.position')
            ->setParameter('storeId', $store->getId())
            ->getQuery()
            ->getResult();
    }
}
