/**
 * CoreShop RuleBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useEffect, useMemo } from 'react'
import { Form, Select, Button, Dropdown, Space, Empty, Card } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import type { MenuProps } from 'antd'
import { container } from '@pimcore/studio-ui-bundle'
import type { ConditionComponentProps } from '../index'
import type { RuleCondition } from '../types'
import { coreshopRuleServiceIds } from '../registry'
import type { ConditionRegistry } from '../registry/ConditionRegistry'
import { ConditionItem } from '../components/ConditionItem'

interface NestedConditionData {
  operator?: 'and' | 'or' | 'not'
  conditions?: RuleCondition[]
}

export const NestedCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange,
  registryId
}) => {
  const conditionData = data as NestedConditionData
  const operator = conditionData.operator || 'and'
  const conditions = conditionData.conditions || []

  useEffect(() => {
    if (conditionData.operator === undefined) {
      onChange({ ...conditionData, operator: 'and' })
    }
  }, [])

  // Use provided registryId or fall back to default
  const effectiveRegistryId = registryId || coreshopRuleServiceIds.conditionRegistry

  const conditionRegistry = useMemo(
    () => container.get<ConditionRegistry>(effectiveRegistryId),
    [effectiveRegistryId]
  )

  const availableTypes = useMemo(() => {
    return Array.from(conditionRegistry.getAll().keys())
  }, [conditionRegistry])

  const handleOperatorChange = (value: 'and' | 'or' | 'not') => {
    onChange({
      ...conditionData,
      operator: value
    })
  }

  const handleConditionsChange = (newConditions: RuleCondition[]) => {
    onChange({
      ...conditionData,
      conditions: newConditions
    })
  }

  const handleAdd = (type: string) => {
    const newCondition: RuleCondition = {
      type,
      configuration: {},
      sort: conditions.length
    }
    handleConditionsChange([...conditions, newCondition])
  }

  const handleChange = (index: number, condition: RuleCondition) => {
    const updated = [...conditions]
    updated[index] = condition
    handleConditionsChange(updated)
  }

  const handleMove = (from: number, to: number) => {
    if (to < 0 || to >= conditions.length) return
    const updated = [...conditions]
    const [moved] = updated.splice(from, 1)
    updated.splice(to, 0, moved)
    // Update sort order
    updated.forEach((c, i) => {
      c.sort = i
    })
    handleConditionsChange(updated)
  }

  const handleDelete = (index: number) => {
    const updated = conditions.filter((_, i) => i !== index)
    // Update sort order
    updated.forEach((c, i) => {
      c.sort = i
    })
    handleConditionsChange(updated)
  }

  const menuItems: MenuProps['items'] = availableTypes.map(type => ({
    key: type,
    label: `Condition: ${type}`,
    onClick: () => handleAdd(type)
  }))

  return (
    <Form layout="vertical">
      <Form.Item label="Operator">
        <Select
          value={operator}
          onChange={handleOperatorChange}
          style={{ width: '100%' }}
          options={[
            { value: 'and', label: 'AND - All conditions must match' },
            { value: 'or', label: 'OR - At least one condition must match' },
            { value: 'not', label: 'NOT - None of the conditions must match' }
          ]}
        />
      </Form.Item>

      <Form.Item label="Nested Conditions">
        <Card size="small" style={{ backgroundColor: '#fafafa' }}>
          <Space direction="vertical" size="middle" style={{ width: '100%' }}>
            <div>
              <Dropdown menu={{ items: menuItems }} placement="bottomLeft">
                <Button type="dashed" icon={<PlusOutlined />} size="small">
                  Add Condition
                </Button>
              </Dropdown>
            </div>

            {conditions.length === 0 ? (
              <Empty
                description="No nested conditions"
                image={Empty.PRESENTED_IMAGE_SIMPLE}
              />
            ) : (
              <div>
                {conditions.map((condition, index) => (
                  <ConditionItem
                    key={index}
                    condition={condition}
                    index={index}
                    total={conditions.length}
                    onChange={(c) => handleChange(index, c)}
                    onMove={handleMove}
                    onDelete={() => handleDelete(index)}
                    registryId={effectiveRegistryId}
                  />
                ))}
              </div>
            )}
          </Space>
        </Card>
      </Form.Item>
    </Form>
  )
}
