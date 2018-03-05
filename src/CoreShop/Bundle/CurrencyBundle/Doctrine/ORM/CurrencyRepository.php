<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CurrencyBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;

class CurrencyRepository extends EntityRepository implements CurrencyRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }

    /**
     * {@inheritdoc}
     */
    public function findActive()
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.countries', 'c')
            ->andWhere('c.active = true')
            ->getQuery()
            ->useResultCache(true)
            ->useQueryCache(true)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($currencyCode)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isoCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->getQuery()
            ->useResultCache(true)
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
