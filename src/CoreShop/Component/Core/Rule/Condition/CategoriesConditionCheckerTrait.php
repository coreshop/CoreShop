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

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

trait CategoriesConditionCheckerTrait
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    protected function getCategoriesToCheck(array $categories, StoreInterface $store, bool $recursive): array
    {
        $categoryIdsToCheck = $categories;

        if ($recursive) {
            $categoryIdsToCheck = $this->categoryRepository->findRecursiveChildCategoryIdsForStoreByCategories($categories, $store);
        }

        return $categoryIdsToCheck;
    }
}
