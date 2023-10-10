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

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface ProductVariantRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * @return ProductInterface[]
     */
    public function findAllVariants(ProductInterface $product, bool $recursive = true): array;

    public function findRecursiveVariantIdsForProductAndStore(ProductInterface $product, StoreInterface $store): array|\Pimcore\Model\DataObject\Listing;

    public function findRecursiveVariantIdsForProductAndStoreByProducts(array $products, StoreInterface $store): array;
}
