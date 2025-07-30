<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Core\Index\Extensions;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

final class ProductClassExtension implements IndexColumnsExtensionInterface
{
    public function __construct(
        private string $productClassName,
    ) {
    }

    public function supports(IndexInterface $index): bool
    {
        return $this->productClassName === $index->getClass() && $index->getWorker() === 'mysql';
    }

    public function getSystemColumns(): array
    {
        return [
            (new Column('categoryIds', Type::getType('string')))->setLength(255),
            (new Column('parentCategoryIds', Type::getType('string')))->setLength(255),
            (new Column('stores', Type::getType('string')))->setLength(255),
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
             * @psalm-suppress InvalidArgument
             */
            $stores = @implode(',', $indexable->getStores() ?? []);

            return [
                'categoryIds' => ',' . implode(',', $categoryIds) . ',',
                'parentCategoryIds' => ',' . implode(',', $parentCategoryIds) . ',',
                'stores' => ',' . $stores . ',',
            ];
        }

        return [];
    }
}
