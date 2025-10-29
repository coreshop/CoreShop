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
import { Tabs, Button, Space, message } from 'antd'
import { SaveOutlined } from '@ant-design/icons'
import type { Rule, RuleConfig } from '../types'
import { ConditionsPanel } from './ConditionsPanel'
import { ActionsPanel } from './ActionsPanel'

export interface RuleFormTab {
  key: string
  label: string
  component: React.ReactNode
  disabled?: boolean
}

interface RuleFormProps {
  rule: Rule
  config: RuleConfig
  settingsComponent: React.ReactNode
  additionalTabs?: RuleFormTab[]
  onSave?: (rule: Rule) => Promise<void>
  onChange: (rule: Rule) => void
  hideToolbar?: boolean
}

export const RuleForm: React.FC<RuleFormProps> = ({
  rule,
  config,
  settingsComponent,
  additionalTabs = [],
  onSave,
  onChange,
  hideToolbar = false
}) => {
  const [saving, setSaving] = React.useState(false)

  const handleSave = async () => {
    if (!onSave) return
    setSaving(true)
    try {
      await onSave(rule)
      message.success('Rule saved successfully')
    } catch (error) {
      message.error('Failed to save rule')
      console.error(error)
    } finally {
      setSaving(false)
    }
  }

  const handleConditionsChange = (conditions: Rule['conditions']) => {
    onChange({ ...rule, conditions })
  }

  const handleActionsChange = (actions: Rule['actions']) => {
    onChange({ ...rule, actions })
  }

  const baseTabs = [
    {
      key: 'settings',
      label: 'Settings',
      children: settingsComponent
    },
    {
      key: 'conditions',
      label: 'Conditions',
      children: (
        <ConditionsPanel
          conditions={rule.conditions || []}
          availableTypes={config.conditions}
          onChange={handleConditionsChange}
        />
      )
    },
    {
      key: 'actions',
      label: 'Actions',
      children: (
        <ActionsPanel
          actions={rule.actions || []}
          availableTypes={config.actions}
          onChange={handleActionsChange}
        />
      )
    }
  ]

  const additionalTabItems = additionalTabs.map(tab => ({
    key: tab.key,
    label: tab.label,
    children: tab.component,
    disabled: tab.disabled
  }))

  const allTabs = [...baseTabs, ...additionalTabItems]

  return (
    <div style={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
      {!hideToolbar && (
        <div style={{ padding: '16px 24px', borderBottom: '1px solid #f0f0f0' }}>
          <Space>
            <Button
              type="primary"
              icon={<SaveOutlined />}
              onClick={handleSave}
              loading={saving}
            >
              Save
            </Button>
          </Space>
        </div>
      )}

      <Tabs
        defaultActiveKey="settings"
        items={allTabs}
        style={{ flex: 1, overflow: 'auto' }}
        tabBarStyle={{ paddingLeft: 24, paddingRight: 24, marginBottom: 0 }}
      />
    </div>
  )
}
