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

namespace CoreShop\Component\Core\Rule\Condition;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

trait ProductVariantsCheckerTrait
{
    private ProductVariantRepositoryInterface $productRepository;

    public function __construct(
        ProductVariantRepositoryInterface $productRepository,
    ) {
        $this->productRepository = $productRepository;
    }

    protected function getProductsToCheck(array $products, StoreInterface $store, bool $includeVariants): array
    {
        $productIdsToCheck = $products;

        if ($includeVariants) {
            $productIdsToCheck = $this->productRepository->findRecursiveVariantIdsForProductAndStoreByProducts($products, $store);
        }

        return $productIdsToCheck;
    }
}
