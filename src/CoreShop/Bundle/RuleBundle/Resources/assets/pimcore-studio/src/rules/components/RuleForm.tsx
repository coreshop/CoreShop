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
import { Tabs, Button, Space } from 'antd'
import { SaveOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import type { Rule, RuleConfig } from '../types'
import { ConditionsPanel } from './ConditionsPanel'
import { ActionsPanel } from './ActionsPanel'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

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
  conditionRegistryId: symbol | string
  actionRegistryId: symbol | string
  currentLocale?: string
  locales?: string[]
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
  hideToolbar = false,
  conditionRegistryId,
  actionRegistryId,
  currentLocale,
  locales
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [saving, setSaving] = React.useState(false)

  const handleSave = async () => {
    if (!onSave) return
    setSaving(true)
    try {
      await onSave(rule)
      void messageApi.success('Rule saved successfully')
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to save rule')))
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
      label: t('coreshop_settings', { defaultValue: 'Settings' }),
      children: settingsComponent
    },
    {
      key: 'conditions',
      label: t('coreshop_conditions', { defaultValue: 'Conditions' }),
      children: (
        <ConditionsPanel
          conditions={rule.conditions || []}
          availableTypes={config.conditions}
          onChange={handleConditionsChange}
          registryId={conditionRegistryId}
          currentLocale={currentLocale}
          locales={locales}
        />
      )
    },
    {
      key: 'actions',
      label: t('coreshop_actions', { defaultValue: 'Actions' }),
      children: (
        <ActionsPanel
          actions={rule.actions || []}
          availableTypes={config.actions}
          onChange={handleActionsChange}
          registryId={actionRegistryId}
          currentLocale={currentLocale}
          locales={locales}
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
              {t('coreshop_save', { defaultValue: 'Save' })}
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
