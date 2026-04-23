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

namespace CoreShop\Bundle\OrderBundle\Grid\Column\Collector;

use Pimcore\Bundle\StudioBackendBundle\Grid\Column\ColumnCollectorInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Schema\ColumnConfiguration;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\ElementTypes;

final class OrderStateCollector implements ColumnCollectorInterface
{
    private const STATE_COLUMNS = [
        'coreshop_order_state' => 'Order State',
        'coreshop_payment_state' => 'Payment State',
        'coreshop_shipping_state' => 'Shipping State',
        'coreshop_invoice_state' => 'Invoice State',
    ];

    public function getCollectorName(): string
    {
        return 'coreshop-order-state';
    }

    public function getColumnConfigurations(array $availableColumnDefinitions): array
    {
        $definition = $availableColumnDefinitions['coreshop.orderState'] ?? null;
        if ($definition === null) {
            return [];
        }

        $columns = [];
        foreach (self::STATE_COLUMNS as $key => $label) {
            $columns[] = new ColumnConfiguration(
                key: $key,
                group: ['CoreShop', 'Order States'],
                sortable: $definition->isSortable(),
                editable: false,
                exportable: $definition->isExportable(),
                filterable: $definition->isFilterable(),
                localizable: false,
                locale: null,
                type: 'coreshop.orderState',
                frontendType: $definition->getFrontendType(),
                config: [],
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
