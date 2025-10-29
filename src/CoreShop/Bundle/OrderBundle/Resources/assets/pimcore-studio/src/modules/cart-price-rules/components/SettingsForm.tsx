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
import { Form, Input, InputNumber, Checkbox, Tabs } from 'antd'
import type { CartPriceRule } from '../types'

interface SettingsFormProps {
  rule: CartPriceRule
  onChange: (rule: CartPriceRule) => void
  languages?: string[]
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  rule,
  onChange,
  languages = ['en']
}) => {
  const handleFieldChange = (field: keyof CartPriceRule, value: any) => {
    onChange({ ...rule, [field]: value })
  }

  const handleTranslationChange = (lang: string, field: keyof CartPriceRule['translations'][string], value: any) => {
    const translations = rule.translations || {}
    onChange({
      ...rule,
      translations: {
        ...translations,
        [lang]: {
          ...translations[lang],
          [field]: value
        }
      }
    })
  }

  const translationTabs = languages.map(lang => ({
    key: lang,
    label: lang.toUpperCase(),
    children: (
      <Form layout="vertical" style={{ padding: '10px 0' }}>
        <Form.Item label="Label">
          <Input
            value={rule.translations?.[lang]?.label || ''}
            onChange={(e) => handleTranslationChange(lang, 'label', e.target.value)}
            placeholder="Enter label"
          />
        </Form.Item>
      </Form>
    )
  }))

  return (
    <div style={{ padding: 24 }}>
      <Form layout="vertical">
        <Form.Item label="Name" required>
          <Input
            value={rule.name}
            onChange={(e) => handleFieldChange('name', e.target.value)}
            placeholder="Enter name"
          />
        </Form.Item>

        <Form.Item label="Description">
          <Input.TextArea
            value={rule.description || ''}
            onChange={(e) => handleFieldChange('description', e.target.value)}
            placeholder="Enter description"
            rows={4}
          />
        </Form.Item>

        <Form.Item>
          <Checkbox
            checked={rule.active}
            onChange={(e) => handleFieldChange('active', e.target.checked)}
          >
            Active
          </Checkbox>
        </Form.Item>

        <Form.Item label="Priority">
          <InputNumber
            value={rule.priority || 0}
            onChange={(value) => handleFieldChange('priority', value || 0)}
            style={{ width: '100%' }}
          />
        </Form.Item>

        <Form.Item>
          <Checkbox
            checked={rule.isVoucherRule || false}
            onChange={(e) => handleFieldChange('isVoucherRule', e.target.checked)}
          >
            Is Voucher Rule
          </Checkbox>
        </Form.Item>

        <Form.Item label="Translations">
          <Tabs
            items={translationTabs}
            size="small"
          />
        </Form.Item>
      </Form>
    </div>
  )
}
