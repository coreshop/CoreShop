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

namespace CoreShop\Component\Core\Index\Extensions;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;

final class ProductClassExtension implements IndexColumnsExtensionInterface
{
    /**
     * @var string
     */
    private $productClassName;

    /**
     * @param string $productClassName
     */
    public function __construct(string $productClassName)
    {
        $this->productClassName = $productClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(IndexInterface $index)
    {
        return $this->productClassName === $index->getClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemColumns()
    {
        return [
            'categoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'parentCategoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'stores' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedSystemColumns()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexColumns(IndexableInterface $indexable)
    {
        if ($indexable instanceof ProductInterface) {
            $categoryIds = [];
            $parentCategoryIds = [];

            $categories = $indexable->getCategories();
            $categories = is_array($categories) ? $categories : [];

            foreach ($categories as $c) {
                if ($c instanceof CategoryInterface) {
                    $categoryIds[$c->getId()] = $c->getId();

                    $parents = $c->getHierarchy();

                    foreach ($parents as $p) {
                        $parentCategoryIds[] = $p->getId();
                    }
                }
            }

            return [
                'categoryIds' => ',' . implode(',', $categoryIds) . ',',
                'parentCategoryIds' => ',' . implode(',', $parentCategoryIds) . ',',
                'stores' => ',' . @implode(',', $indexable->getStores()) . ',',
            ];
        }

        return [];
    }
}
