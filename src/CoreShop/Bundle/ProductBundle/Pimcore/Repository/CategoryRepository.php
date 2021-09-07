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

namespace CoreShop\Bundle\ProductBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Product\Repository\CategoryRepositoryInterface;

class CategoryRepository extends PimcoreRepository implements CategoryRepositoryInterface
{
    public function findFirstLevel(): array
    {
        $list = $this->getList();
        $list->setCondition('parentCategory__id is null');

        return $list->getObjects();
    }

    public function findChildCategories(CategoryInterface $category): array
    {
        $list = $this->getList();
        $list->setCondition('parentCategory__id = ?', [$category->getId()]);

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

        return $list->getObjects();
    }
}
