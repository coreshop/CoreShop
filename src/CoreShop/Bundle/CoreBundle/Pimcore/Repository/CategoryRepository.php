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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Pimcore\Repository;

use CoreShop\Bundle\ProductBundle\Pimcore\Repository\CategoryRepository as BaseCategoryRepository;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\DataObject\Listing;

class CategoryRepository extends BaseCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForStore(StoreInterface $store): array
    {
        $list = $this->getList();
        $list->setCondition('stores LIKE ?', ['%,' . $store->getId() . ',%']);
        $this->setSortingForListingWithoutCategory($list);

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findFirstLevelForStore(StoreInterface $store): array
    {
        $list = $this->getList();
        $list->setCondition('parentCategory__id is null AND stores LIKE "%,' . $store->getId() . ',%"');

        $this->setSortingForListingWithoutCategory($list);

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findChildCategoriesForStore(CategoryInterface $category, StoreInterface $store): array
    {
        $list = $this->getList();
        $list->setCondition('parentCategory__id = ? AND stores LIKE "%,' . $store->getId() . ',%"', [$category->getId()]);

        $this->setSortingForListing($list, $category);

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findRecursiveChildCategoryIdsForStore(CategoryInterface $category, StoreInterface $store): array
    {
        $list = $this->getList();
        $dao = $list->getDao();

        $db = \Pimcore\Db::get();
        $query = $db->select()
            ->from($dao->getTableName(), ['oo_id'])
            ->where('o_path LIKE ?', $category->getRealFullPath() . '/%')
            ->where('stores LIKE ?', '%,' . $store->getId() . ',%');

        $childIds = [];

        foreach ($query->execute()->fetchAllAssociative() as $column) {
            $childIds[] = $column['oo_id'];
        }

        return $childIds;
    }

    /**
     * {@inheritdoc}
     */
    public function findRecursiveChildCategoriesForStore(CategoryInterface $category, StoreInterface $store): array
    {
        $childIds = $this->findRecursiveChildCategoryIdsForStore($category, $store);

        if (empty($childIds)) {
            return [];
        }

        $list = $this->getList();
        $list->setCondition('oo_id IN (' . implode(',', $childIds) . ')');

        $this->setSortingForListing($list, $category);

        return $list->getObjects();
    }

    private function setSortingForListing(Listing $list, CategoryInterface $category): void
    {
        if (method_exists($category, 'getChildrenSortBy')) {
            $list->setOrderKey(
                sprintf('o_%s ASC', $category->getChildrenSortBy()),
                false
            );
        } else {
            $list->setOrderKey(
                'o_key ASC',
                false
            );
        }
    }

    private function setSortingForListingWithoutCategory(Listing $list): void
    {
        $list->setOrderKey(
            'o_key ASC',
            false
        );
    }
}
