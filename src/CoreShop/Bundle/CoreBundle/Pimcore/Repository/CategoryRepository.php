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
    public function findForStore(StoreInterface $store)
    {

        $list = $this->getList();
        $list->setCondition('stores LIKE ?', ['%,' . $store->getId() . ',%']);
        $this->setSortingForListingWithoutCategory($list);

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findFirstLevelForStore(StoreInterface $store)
    {
        $list = $this->getList();
        $list->setCondition('parentCategory__id is null AND stores LIKE "%,' . $store->getId() . ',%"');

        $this->setSortingForListingWithoutCategory($list);

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findChildCategoriesForStore(CategoryInterface $category, StoreInterface $store)
    {
        $list = $this->getList();
        $list->setCondition('parentCategory__id = ? AND stores LIKE "%,' . $store->getId() . ',%"', [$category->getId()]);

        $this->setSortingForListing($list, $category);

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function findRecursiveChildCategoryIdsForStore(CategoryInterface $category, StoreInterface $store)
    {
        $list = $this->getList();

        $db = \Pimcore\Db::get();
        $query = $db->select()
            ->from($list->getTableName(), ['oo_id'])
            ->where('o_path LIKE ?', $category->getRealFullPath() . '/%')
            ->where('stores LIKE ?', '%,' . $store->getId() . ',%');

        $childIds = [];

        foreach ($query->execute()->fetchAll() as $column) {
            $childIds[] = $column['oo_id'];
        }

        return $childIds;
    }

    /**
     * {@inheritdoc}
     */
    public function findRecursiveChildCategoriesForStore(CategoryInterface $category, StoreInterface $store)
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

    /**
     * @param Listing $list
     * @param CategoryInterface $category
     */
    private function setSortingForListing(Listing $list, CategoryInterface $category)
    {
        //TODO: fix as soon as CoreShop requires pimcore/core-version:~5.2.2 as minimum
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

    /**
     * @param Listing $list
     */
    private function setSortingForListingWithoutCategory(Listing $list)
    {
        $list->setOrderKey(
            'o_key ASC',
            false
        );
    }
}