/**
 * CoreShop ProductBundle Studio Plugin
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
import { Form, Input, InputNumber, Checkbox, Space, Typography } from 'antd'
import { useTranslation } from 'react-i18next'
import { LocalizedFieldsProvider } from '@coreshop/resource/src/components/localization/localized-fields'
import type { ProductPriceRule } from '../types'

interface SettingsFormProps {
  rule: ProductPriceRule
  onChange: (rule: ProductPriceRule) => void
  currentLocale: string
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  rule,
  onChange,
  currentLocale
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  React.useEffect(() => {
    const initial: any = { ...(rule ?? {}) }

    // Ensure translations structure for current locale
    initial.translations = initial.translations ?? {}
    if (!initial.translations[currentLocale]) {
      initial.translations[currentLocale] = { locale: currentLocale, label: '' }
    }

    form.setFieldsValue(initial)
  }, [rule, currentLocale])

  return (
    <div style={{ padding: 12 }}>
      <Space align='baseline' style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
        <Typography.Text type='secondary'>Translations</Typography.Text>
        <Typography.Text type='secondary'>{currentLocale.toUpperCase()}</Typography.Text>
      </Space>

      <Form
        form={form}
        layout="vertical"
        onValuesChange={(_, allValues) => {
          // Preserve existing translations and only merge the edited ones
          const mergedTranslations = {
            ...(rule?.translations ?? {}),
            ...(allValues?.translations ?? {})
          }
          onChange({ ...allValues, translations: mergedTranslations })
        }}
      >
        <LocalizedFieldsProvider locales={[currentLocale]}>
          <Form.Item label={`Label (${currentLocale.toUpperCase()})`} name={['translations', currentLocale, 'label']}>
            <Input />
          </Form.Item>
        </LocalizedFieldsProvider>

        <Form.Item label="Name" name="name" rules={[{ required: true }]}>
          <Input />
        </Form.Item>

        <Form.Item label="Description" name="description">
          <Input.TextArea
            rows={4}
          />
        </Form.Item>

        <Form.Item name="active" valuePropName="checked">
          <Checkbox>Active</Checkbox>
        </Form.Item>

        <Form.Item label={t('coreshop_priority', { defaultValue: 'Priority' })} name="priority">
          <InputNumber style={{ width: '100%' }} />
        </Form.Item>
      </Form>
    </div>
  )
}
