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

namespace CoreShop\Bundle\ProductBundle\Doctrine\ORM;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;

class ProductSpecificPriceRuleRepository extends PriceRuleRepository implements ProductSpecificPriceRuleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForProduct(ProductInterface $product)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.product = :productId')
            ->setParameter('productId', $product->getId())
            ->addOrderBy('o.priority', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
