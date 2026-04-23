/**
 * CoreShop IndexBundle Filter Condition Item
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
import type { FilterCondition } from '../types'
import type { ConditionRegistry } from '../conditions/ConditionRegistry'

interface ConditionItemProps {
  condition: FilterCondition
  index: number
  total: number
  onChange: (condition: FilterCondition) => void
  onMove: (from: number, to: number) => void
  onDelete: () => void
  registryId: symbol | string
  indexId?: number
}

export const ConditionItem: React.FC<ConditionItemProps> = ({
  condition,
  index,
  total,
  onChange,
  onMove,
  onDelete,
  registryId,
  indexId
}) => {
  const handleDataChange = (data: Partial<FilterCondition>) => {
    onChange({ ...condition, ...data })
  }

  const conditionRegistry = useMemo(
    () => container.get<ConditionRegistry>(registryId),
    [registryId]
  )

  const ConditionComponent = conditionRegistry.get(condition.type)

  const title = (
    <Space>
      <span style={{ fontWeight: 600 }}>
        Filter: {condition.type}
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
          data={condition}
          onChange={handleDataChange}
          indexId={indexId}
          registryId={registryId}
        />
      ) : (
        <div style={{ color: '#999', fontStyle: 'italic' }}>
          Unknown condition type: {condition.type}
        </div>
      )}
    </Card>
  )
}
