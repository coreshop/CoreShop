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

namespace CoreShop\Component\Core\Rule\Condition;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

trait CategoriesConditionCheckerTrait
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array          $categories
     * @param StoreInterface $store
     * @param bool           $recursive
     *
     * @return array
     */
    protected function getCategoriesToCheck($categories, StoreInterface $store, $recursive)
    {
        $categoryIdsToCheck = $categories;

        if ($recursive) {
            foreach ($categories as $categoryId) {
                $category = $this->categoryRepository->find($categoryId);

                if (!$category instanceof CategoryInterface) {
                    continue;
                }

                $subCategories = $this->categoryRepository->findRecursiveChildCategoryIdsForStore($category, $store);

                foreach ($subCategories as $child) {
                    if (!in_array($child, $categoryIdsToCheck)) {
                        $categoryIdsToCheck[] = $child;
                    }
                }
            }
        }

        return $categoryIdsToCheck;
    }
}
