/**
 * CoreShop NotificationBundle Studio Plugin
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
import { Form, Select } from 'antd'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import { useTranslation } from 'react-i18next'
import type { NotificationRule, NotificationRuleType } from '../types'

export interface SettingsFormProps {
  rule: NotificationRule
  onChange: (draft: Partial<NotificationRule>) => void
  types: NotificationRuleType[]
  currentLocale: string
  locales?: string[]
  onTypeChange?: (type: NotificationRuleType) => void
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  rule,
  onChange,
  types,
  currentLocale,
  onTypeChange
}) => {
  const { t } = useTranslation()

  const typeOptions = types.map(type => ({
    value: type,
    label: t(`coreshop_notification_rule_type_${type}`, { defaultValue: type.charAt(0).toUpperCase() + type.slice(1) })
  }))

  const handleTypeChange = (value: NotificationRuleType) => {
    if (onTypeChange) {
      onTypeChange(value)
    }
  }

  const handleFormChange = (draft: Partial<NotificationRule>) => {
    onChange(draft)
  }

  return (
    <div style={{ padding: 12 }}>
      {/* Type selector - handled separately due to dynamic options and reset logic */}
      <Form layout='vertical'>
        <Form.Item
          label={t('coreshop_notification_rule_type', { defaultValue: 'Type' })}
          required
        >
          <Select
            value={rule.type}
            options={typeOptions}
            placeholder={t('coreshop_select_type', { defaultValue: 'Select a type' })}
            onChange={handleTypeChange}
          />
        </Form.Item>
      </Form>

      {/* Other fields via SchemaForm */}
      <SchemaForm<NotificationRule>
        blockPrefix="coreshop_notification_rule"
        data={rule}
        onChange={handleFormChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
