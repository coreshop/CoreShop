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

namespace CoreShop\Bundle\AddressBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Address\Repository\AddressIdentifierRepositoryInterface;

class AddressIdentifierRepository extends EntityRepository implements AddressIdentifierRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByName($name): ?AddressIdentifierInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true)
            ->getOneOrNullResult();
    }
}
