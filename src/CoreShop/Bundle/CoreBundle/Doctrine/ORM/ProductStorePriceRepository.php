<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Doctrine\ORM;

use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductStorePriceRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class ProductStorePriceRepository extends EntityRepository implements ProductStorePriceRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForProduct(ProductInterface $product)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :product')
            ->setParameter('product', $product->getId())
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findForProductAndProperty(ProductInterface $product, string $property)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :product')
            ->andWhere('o.property = :property')
            ->setParameter('product', $product->getId())
            ->setParameter('property', $property)
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findForProductAndStoreAndProperty(ProductInterface $product, StoreInterface $store, string $property)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :product')
            ->andWhere('o.store = :store')
            ->andWhere('o.property = :property')
            ->setParameter('product', $product->getId())
            ->setParameter('store', $store)
            ->setParameter('property', $property)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
