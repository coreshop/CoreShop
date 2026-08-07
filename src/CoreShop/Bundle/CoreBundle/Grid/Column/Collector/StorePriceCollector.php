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

namespace CoreShop\Bundle\CoreBundle\Grid\Column\Collector;

use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\ColumnCollectorInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Schema\ColumnConfiguration;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\ElementTypes;

final class StorePriceCollector implements ColumnCollectorInterface
{
    public function __construct(
        private readonly StoreRepositoryInterface $storeRepository,
    ) {
    }

    public function getCollectorName(): string
    {
        return 'coreshop-store-price';
    }

    public function getColumnConfigurations(array $availableColumnDefinitions): array
    {
        $definition = $availableColumnDefinitions['coreshop.storePrice'] ?? null;
        if ($definition === null) {
            return [];
        }

        $stores = $this->storeRepository->findAll();
        $columns = [];

        /** @var StoreInterface $store */
        foreach ($stores as $store) {
            $columns[] = new ColumnConfiguration(
                key: 'coreshop_store_price_' . $store->getId(),
                group: ['CoreShop', 'Store Prices'],
                sortable: $definition->isSortable(),
                editable: false,
                exportable: $definition->isExportable(),
                filterable: $definition->isFilterable(),
                localizable: false,
                locale: null,
                type: 'coreshop.storePrice',
                frontendType: $definition->getFrontendType(),
                config: [
                    'storeId' => $store->getId(),
                    'storeName' => $store->getName(),
                ],
            );
        }

        return $columns;
    }

    public function supportedElementTypes(): array
    {
        return [
            ElementTypes::TYPE_DATA_OBJECT,
        ];
    }
}
