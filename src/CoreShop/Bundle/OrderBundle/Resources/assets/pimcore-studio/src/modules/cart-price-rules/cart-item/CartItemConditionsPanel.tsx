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

import React from 'react'
import { Button, Dropdown, Space, Empty } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import type { MenuProps } from 'antd'
import { useTranslation } from 'react-i18next'
import type { RuleCondition } from '@coreshop/rule/src/rules/types'
import { CartItemConditionItem } from './CartItemConditionItem'

interface CartItemConditionsPanelProps {
  conditions: RuleCondition[]
  availableTypes: string[]
  onChange: (conditions: RuleCondition[]) => void
}

export const CartItemConditionsPanel: React.FC<CartItemConditionsPanelProps> = ({
  conditions,
  availableTypes,
  onChange
}) => {
  const { t } = useTranslation()

  const handleAdd = (type: string) => {
    const newCondition: RuleCondition = {
      type,
      configuration: {},
      sort: conditions.length
    }
    onChange([...conditions, newCondition])
  }

  const handleChange = (index: number, condition: RuleCondition) => {
    const updated = [...conditions]
    updated[index] = condition
    onChange(updated)
  }

  const handleMove = (from: number, to: number) => {
    if (to < 0 || to >= conditions.length) return
    const updated = [...conditions]
    const [moved] = updated.splice(from, 1)
    updated.splice(to, 0, moved)
    updated.forEach((c, i) => {
      c.sort = i
    })
    onChange(updated)
  }

  const handleDelete = (index: number) => {
    const updated = conditions.filter((_, i) => i !== index)
    updated.forEach((c, i) => {
      c.sort = i
    })
    onChange(updated)
  }

  const menuItems: MenuProps['items'] = availableTypes.map(type => ({
    key: type,
    label: type,
    onClick: () => handleAdd(type)
  }))

  return (
    <div style={{ padding: 24 }}>
      <Space direction="vertical" size="large" style={{ width: '100%' }}>
        <div>
          <Dropdown menu={{ items: menuItems }} placement="bottomLeft">
            <Button type="primary" icon={<PlusOutlined />}>
              {t('coreshop_cart_item_add_condition', { defaultValue: 'Add Condition' })}
            </Button>
          </Dropdown>
        </div>

        {conditions.length === 0 ? (
          <Empty
            description={t('coreshop_cart_item_no_conditions', { defaultValue: 'No conditions defined' })}
            image={Empty.PRESENTED_IMAGE_SIMPLE}
          />
        ) : (
          <div>
            {conditions.map((condition, index) => (
              <CartItemConditionItem
                key={index}
                condition={condition}
                index={index}
                total={conditions.length}
                onChange={(c) => handleChange(index, c)}
                onMove={handleMove}
                onDelete={() => handleDelete(index)}
              />
            ))}
          </div>
        )}
      </Space>
    </div>
  )
}
