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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Index\Extensions;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\OpenSearchWorkerInterface;

final class OpenSearchProductClassExtension implements IndexColumnsExtensionInterface
{
    public function __construct(
        private string $productClassName,
    ) {
    }

    public function supports(IndexInterface $index): bool
    {
        return $this->productClassName === $index->getClass() && $index->getWorker() === 'opensearch';
    }

    public function getSystemColumns(): array
    {
        return [
            'categoryIds' => OpenSearchWorkerInterface::FIELD_TYPE_INTEGER,
            'parentCategoryIds' => OpenSearchWorkerInterface::FIELD_TYPE_INTEGER,
            'stores' => OpenSearchWorkerInterface::FIELD_TYPE_INTEGER,
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

            /**
             * @var int[]|string[] $stores
             */
            $stores = $indexable->getStores() ?? [];
            $stores = array_map(static function (int|string $storeId) {
                return (int) $storeId;
            }, $stores);

            return [
                'categoryIds' => array_values($categoryIds),
                'parentCategoryIds' => $parentCategoryIds,
                'stores' => $stores,
            ];
        }

        return [];
    }
}
