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

namespace CoreShop\Bundle\OrderBundle\Grid\Column\Resolver;

use CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\ColumnResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\CoreElementColumnResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Schema\Column;
use Pimcore\Bundle\StudioBackendBundle\Grid\Schema\ColumnData;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\ElementTypes;
use Pimcore\Model\Element\ElementInterface;

final class OrderStateResolver implements ColumnResolverInterface, CoreElementColumnResolverInterface
{
    private const COLUMNS = [
        'coreshop_order_state' => ['getter' => 'getOrderState', 'workflow' => 'coreshop_order'],
        'coreshop_payment_state' => ['getter' => 'getPaymentState', 'workflow' => 'coreshop_order_payment'],
        'coreshop_shipping_state' => ['getter' => 'getShippingState', 'workflow' => 'coreshop_order_shipment'],
        'coreshop_invoice_state' => ['getter' => 'getInvoiceState', 'workflow' => 'coreshop_order_invoice'],
    ];

    public function __construct(
        private readonly WorkflowStateInfoManagerInterface $workflowManager,
    ) {
    }

    public function resolveForCoreElement(Column $column, ElementInterface $element): ColumnData
    {
        if (!$element instanceof OrderInterface) {
            return $this->empty($column);
        }

        $columnDef = self::COLUMNS[$column->getKey()] ?? null;
        if ($columnDef === null) {
            return $this->empty($column);
        }

        $getter = $columnDef['getter'];
        $rawState = $element->{$getter}();

        if (!is_string($rawState) || $rawState === '') {
            return $this->empty($column);
        }

        $stateInfo = $this->workflowManager->getStateInfo(
            $columnDef['workflow'],
            $rawState,
            false,
            $column->getLocale(),
        );

        $label = $stateInfo['label'] ?? $rawState;
        $color = $stateInfo['color'] ?? null;

        return new ColumnData(
            key: $column->getKey(),
            locale: $column->getLocale(),
            value: [
                'label' => $label,
                'color' => $color,
            ],
            fieldType: 'coreshop.orderState',
        );
    }

    public function getType(): string
    {
        return 'coreshop.orderState';
    }

    public function supportedElementTypes(): array
    {
        return [
            ElementTypes::TYPE_OBJECT,
        ];
    }

    private function empty(Column $column): ColumnData
    {
        return new ColumnData(
            key: $column->getKey(),
            locale: $column->getLocale(),
            value: null,
            fieldType: 'coreshop.orderState',
        );
    }
}
