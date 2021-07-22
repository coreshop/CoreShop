<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\StoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;

class StoreRepository extends EntityRepository implements StoreRepositoryInterface
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
    public function findOneBySite($siteId)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findStandard()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isDefault = true')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
