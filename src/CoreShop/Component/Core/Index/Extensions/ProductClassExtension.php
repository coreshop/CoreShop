<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Index\Extensions;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;

final class ProductClassExtension implements IndexColumnsExtensionInterface
{
    private string $productClassName;

    public function __construct(string $productClassName)
    {
        $this->productClassName = $productClassName;
    }

    public function supports(IndexInterface $index): bool
    {
        return $this->productClassName === $index->getClass();
    }

    public function getSystemColumns(): array
    {
        return [
            'categoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'parentCategoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'stores' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }

    public function getLocalizedSystemColumns(): array
    {
        return [];
    }

    public function getIndexColumns(IndexableInterface $indexable): array
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
