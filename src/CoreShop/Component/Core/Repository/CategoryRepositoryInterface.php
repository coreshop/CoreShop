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
     *
     * @return CategoryInterface[]
     */
    public function findChildCategoriesForStore(CategoryInterface $category, StoreInterface $store): array;

    /**
     *
     * @return int[]
     */
    public function findRecursiveChildCategoryIdsForStore(CategoryInterface $category, StoreInterface $store): array;

    /**
     *
     * @return CategoryInterface[]
     */
    public function findRecursiveChildCategoriesForStore(CategoryInterface $category, StoreInterface $store): array;
}
