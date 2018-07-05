<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Rule\Condition;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

trait CategoriesConditionCheckerTrait
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreContextInterface       $storeContext
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository, StoreContextInterface $storeContext)
    {
        $this->categoryRepository = $categoryRepository;
        $this->storeContext = $storeContext;
    }

    /**
     * @param $categories
     * @param $recursive
     *
     * @return array
     */
    protected function getCategoriesToCheck($categories, $recursive)
    {
        $categoryIdsToCheck = $categories;

        if ($recursive) {
            foreach ($categories as $categoryId) {
                $category = $this->categoryRepository->find($categoryId);

                if (!$category instanceof CategoryInterface) {
                    continue;
                }

                $subCategories = $this->categoryRepository->findRecursiveChildCategoryIdsForStore($category, $this->storeContext->getStore());

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
