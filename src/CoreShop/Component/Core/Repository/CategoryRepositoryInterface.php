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

use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Product\Repository\CategoryRepositoryInterface as BaseCategoryRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface CategoryRepositoryInterface extends BaseCategoryRepositoryInterface
{
    /**
     * @return CategoryInterface[]
     */
    public function findForStore(StoreInterface $store): array;

    /**
     * @return CategoryInterface[]
     */
    public function findFirstLevelForStore(StoreInterface $store): array;

    /**
     * @return CategoryInterface[]
     */
    public function findChildCategoriesForStore(CategoryInterface $category, StoreInterface $store): array;

    /**
     * @return int[]
     */
    public function findRecursiveChildCategoryIdsForStore(CategoryInterface $category, StoreInterface $store): array;

    /**
     * @return int[]
     */
    public function findRecursiveChildCategoryIdsForStoreByCategories(array $categories, StoreInterface $store): array;

    /**
     * @return CategoryInterface[]
     */
    public function findRecursiveChildCategoriesForStore(CategoryInterface $category, StoreInterface $store): array;
}
