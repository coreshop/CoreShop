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

namespace CoreShop\Component\Core\Rule\Condition;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductVariantRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

trait ProductVariantsCheckerTrait
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductVariantRepositoryInterface $productRepository
     */
    public function __construct(ProductVariantRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param array          $products
     * @param StoreInterface $store
     * @param bool           $includeVariants
     *
     * @return array
     */
    protected function getProductsToCheck($products, StoreInterface $store, $includeVariants)
    {
        $productIdsToCheck = $products;

        if ($includeVariants) {
            foreach ($products as $productId) {
                $product = $this->productRepository->find($productId);

                if (!$product instanceof ProductInterface) {
                    continue;
                }

                $variants = $this->productRepository->findRecursiveVariantIdsForProductAndStore($product, $store);

                foreach ($variants as $variant) {
                    if (!in_array($variant, $productIdsToCheck)) {
                        $productIdsToCheck[] = $variant;
                    }
                }
            }
        }

        return $productIdsToCheck;
    }
}
