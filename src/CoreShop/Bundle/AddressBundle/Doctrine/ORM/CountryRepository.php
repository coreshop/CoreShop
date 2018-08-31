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

namespace CoreShop\Bundle\AddressBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Address\Repository\CountryRepositoryInterface;

class CountryRepository extends EntityRepository implements CountryRepositoryInterface
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
    public function findByName($name, $locale)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByCode($code)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isoCode= :isoCode')
            ->setParameter('isoCode', $code)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getOneOrNullResult();
    }
}
