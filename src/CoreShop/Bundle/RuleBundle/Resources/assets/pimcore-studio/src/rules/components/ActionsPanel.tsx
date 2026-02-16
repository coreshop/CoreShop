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

import React from 'react'
import { Button, Dropdown, Space, Empty } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import type { MenuProps } from 'antd'
import type { RuleAction } from '../types'
import { ActionItem } from './ActionItem'
import { formatTypeLabel } from './type-label'

interface ActionsPanelProps {
  actions: RuleAction[]
  availableTypes: string[]
  onChange: (actions: RuleAction[]) => void
  registryId: symbol | string
  currentLocale?: string
  locales?: string[]
}

export const ActionsPanel: React.FC<ActionsPanelProps> = ({
  actions,
  availableTypes,
  onChange,
  registryId,
  currentLocale,
  locales
}) => {
  const handleAdd = (type: string) => {
    const newAction: RuleAction = {
      type,
      configuration: {},
      sort: actions.length
    }
    onChange([...actions, newAction])
  }

  const handleChange = (index: number, action: RuleAction) => {
    const updated = [...actions]
    updated[index] = action
    onChange(updated)
  }

  const handleMove = (from: number, to: number) => {
    if (to < 0 || to >= actions.length) return
    const updated = [...actions]
    const [moved] = updated.splice(from, 1)
    updated.splice(to, 0, moved)
    // Update sort order
    updated.forEach((a, i) => {
      a.sort = i
    })
    onChange(updated)
  }

  const handleDelete = (index: number) => {
    const updated = actions.filter((_, i) => i !== index)
    // Update sort order
    updated.forEach((a, i) => {
      a.sort = i
    })
    onChange(updated)
  }

  const menuItems: MenuProps['items'] = availableTypes.map(type => ({
    key: type,
    label: formatTypeLabel('Action', type),
    onClick: () => handleAdd(type)
  }))

  return (
    <div style={{ padding: 24 }}>
      <Space direction="vertical" size="large" style={{ width: '100%' }}>
        <div>
          <Dropdown menu={{ items: menuItems }} placement="bottomLeft">
            <Button type="primary" icon={<PlusOutlined />}>
              Add Action
            </Button>
          </Dropdown>
        </div>

        {actions.length === 0 ? (
          <Empty
            description="No actions defined"
            image={Empty.PRESENTED_IMAGE_SIMPLE}
          />
        ) : (
          <div>
            {actions.map((action, index) => (
              <ActionItem
                key={index}
                action={action}
                index={index}
                total={actions.length}
                onChange={(a) => handleChange(index, a)}
                onMove={handleMove}
                onDelete={() => handleDelete(index)}
                registryId={registryId}
                currentLocale={currentLocale}
                locales={locales}
              />
            ))}
          </div>
        )}
      </Space>
    </div>
  )
}
