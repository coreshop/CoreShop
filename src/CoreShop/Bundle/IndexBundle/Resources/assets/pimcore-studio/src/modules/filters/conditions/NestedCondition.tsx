/**
 * CoreShop IndexBundle Nested Filter Condition
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
import { Form, Input, Button, Dropdown, Space, Empty, Card } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import type { MenuProps } from 'antd'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import type { ConditionProps, FilterCondition } from '../types'
import type { ConditionRegistry } from './ConditionRegistry'
import { ConditionItem } from '../components/ConditionItem'

/**
 * Nested Condition - Contains other filter conditions (recursive)
 */
export const NestedCondition: React.FC<ConditionProps> = ({
  data,
  onChange,
  indexId,
  registryId
}) => {
  const { t } = useTranslation()
  const conditions = (data.configuration?.conditions as FilterCondition[]) || []

  const conditionRegistry = useMemo(
    () => {
      if (!registryId) return null
      try {
        return container.get<ConditionRegistry>(registryId)
      } catch {
        // Fallback if registry not found
        return null
      }
    },
    [registryId]
  )

  const availableTypes = useMemo(() => {
    if (!conditionRegistry) return []
    return Array.from(conditionRegistry.getAll()).map(([type]) => type)
  }, [conditionRegistry])

  const handleConditionsChange = (newConditions: FilterCondition[]) => {
    onChange({
      configuration: {
        ...data.configuration,
        conditions: newConditions
      }
    })
  }

  const handleAdd = (type: string) => {
    const newCondition: FilterCondition = {
      type,
      configuration: {},
      sort: conditions.length
    }
    handleConditionsChange([...conditions, newCondition])
  }

  const handleChange = (index: number, condition: FilterCondition) => {
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
    label: `Filter: ${type}`,
    onClick: () => handleAdd(type)
  }))

  return (
    <Form layout="vertical">
      <Form.Item label={t('coreshop_label', { defaultValue: 'Label' })}>
        <Input
          value={data.label}
          onChange={(e) => onChange({ label: e.target.value })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_filters_conditions', { defaultValue: 'Nested Conditions' })}>
        <Card size="small" style={{ backgroundColor: '#fafafa' }}>
          <Space direction="vertical" size="middle" style={{ width: '100%' }}>
            <div>
              <Dropdown menu={{ items: menuItems }} placement="bottomLeft">
                <Button type="dashed" icon={<PlusOutlined />} size="small">
                  {t('coreshop_filters_add_condition', { defaultValue: 'Add Condition' })}
                </Button>
              </Dropdown>
            </div>

            {conditions.length === 0 ? (
              <Empty
                description={t('coreshop_filters_no_conditions', { defaultValue: 'No nested conditions' })}
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
                    registryId={registryId!}
                    indexId={indexId}
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
