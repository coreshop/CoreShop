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

declare(strict_types=1);

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
            ->getResult();
    }
}
