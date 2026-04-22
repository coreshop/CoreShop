/**
 * CoreShop OrderBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useMemo } from 'react'
import { Card, Button, Space } from 'antd'
import { ArrowUpOutlined, ArrowDownOutlined, DeleteOutlined } from '@ant-design/icons'
import { container } from '@pimcore/studio-ui-bundle'
import type { RuleCondition } from '@coreshop/rule/src/rules/types'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopOrderServiceIds } from '../service-ids'

interface CartItemConditionItemProps {
  condition: RuleCondition
  index: number
  total: number
  onChange: (condition: RuleCondition) => void
  onMove: (from: number, to: number) => void
  onDelete: () => void
}

export const CartItemConditionItem: React.FC<CartItemConditionItemProps> = ({
  condition,
  index,
  total,
  onChange,
  onMove,
  onDelete
}) => {
  const handleDataChange = (configuration: Record<string, any>) => {
    onChange({ ...condition, configuration })
  }

  const cartItemConditionRegistry = useMemo(
    () => container.get<ConditionRegistry>(coreshopOrderServiceIds.cartItemConditionRegistry),
    []
  )

  const ConditionComponent = cartItemConditionRegistry.get(condition.type)

  const title = (
    <Space>
      <span style={{ fontWeight: 600 }}>
        Condition: {condition.type}
      </span>
    </Space>
  )

  const extra = (
    <Space>
      <Button
        type="text"
        size="small"
        icon={<ArrowUpOutlined />}
        disabled={index === 0}
        onClick={() => onMove(index, index - 1)}
      />
      <Button
        type="text"
        size="small"
        icon={<ArrowDownOutlined />}
        disabled={index === total - 1}
        onClick={() => onMove(index, index + 1)}
      />
      <Button
        type="text"
        size="small"
        icon={<DeleteOutlined />}
        danger
        onClick={onDelete}
      />
    </Space>
  )

  return (
    <Card
      size="small"
      title={title}
      extra={extra}
      style={{ marginBottom: 8 }}
    >
      {ConditionComponent ? (
        <ConditionComponent
          data={condition.configuration}
          onChange={handleDataChange}
        />
      ) : (
        <div style={{ color: '#999', fontStyle: 'italic' }}>
          Unknown condition type: {condition.type}
        </div>
      )}
    </Card>
  )
}
