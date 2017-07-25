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

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class CarrierRepository extends EntityRepository implements CarrierRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForStore(StoreInterface $store)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.stores', 's')
            ->andWhere('s.id = :store')
            ->setParameter('store', [$store])
            ->getQuery()
            ->useResultCache(true)
            ->useQueryCache(true)
            ->getResult()
        ;
    }
}
