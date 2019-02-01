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

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface ProductVariantRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * @param ProductInterface $product
     * @param bool             $recursive
     * @return ProductInterface[]
     */
    public function findAllVariants(ProductInterface $product, $recursive = true);

    /**
     * @param ProductInterface $product
     * @param StoreInterface   $store
     * @return array
     */
    public function findRecursiveVariantIdsForProductAndStore(ProductInterface $product, StoreInterface $store);
}
