<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Core\Repository\ProductStoreValuesRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class ProductStoreValuesRepository extends EntityRepository implements ProductStoreValuesRepositoryInterface
{
    public function findForProduct(ProductInterface $product): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :product')
            ->setParameter('product', $product->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findForProductAndStore(ProductInterface $product, StoreInterface $store): ?ProductStoreValuesInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :product')
            ->andWhere('o.store = :store')
            ->setParameter('product', $product->getId())
            ->setParameter('store', $store)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
