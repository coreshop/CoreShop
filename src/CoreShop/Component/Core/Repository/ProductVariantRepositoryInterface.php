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
}
