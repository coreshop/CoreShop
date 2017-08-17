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

class CategoryRepository extends BaseCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findForStore(StoreInterface $store)
    {
        return $this->findBy([["condition" => "stores LIKE ?", "variable" => "%" . $store->getId() . "%"]]);
    }

    /**
     * {@inheritdoc}
     */
    public function findFirstLevelForStore(StoreInterface $store)
    {
        $list = $this->getList();
        $list->setCondition("parentCategory__id is null AND stores LIKE '%," . $store->getId().",%'");

        return $list->getObjects();
    }

    /**
     * {@inheritdoc}
     */
    public function getChildCategoriesForStore(CategoryInterface $category, StoreInterface $store)
    {
        $list = $this->getList();
        $list->setCondition("parentCategory__id = ? AND stores LIKE '%," . $store->getId().",%'", [$category->getId()]);

        return $list->getObjects();
    }
}