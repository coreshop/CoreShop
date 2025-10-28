/**
 * CoreShop TaxationBundle Studio Plugin
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
import { Form, Input, InputNumber, Switch, Space, Typography } from 'antd'
import type { TaxRateDetail } from './api'
import { LocalizedFieldsProvider } from '@coreshop/resource/src/components/localization/localized-fields'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export const TaxRateForm: React.FC<{
  data?: TaxRateDetail
  onChange: (draft: Partial<TaxRateDetail>) => void
  currentLocale: string
}> = ({ data, onChange, currentLocale }) => {
  const [form] = Form.useForm()

  React.useEffect(() => {
    const initial: any = { ...(data ?? {}) }
    if (typeof initial.active === 'undefined') initial.active = false
    if (typeof initial.rate === 'undefined') initial.rate = 0
    initial.translations = initial.translations ?? {}
    if (!initial.translations[currentLocale]) {
      initial.translations[currentLocale] = { locale: currentLocale, name: data?.name ?? '' }
    }
    form.setFieldsValue(initial)
  }, [data, currentLocale])

  return (
    <div style={{ padding: 12 }}>
      <Space align='baseline' style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
        <Typography.Text type='secondary'>Translations</Typography.Text>
        <Typography.Text type='secondary'>{ currentLocale.toUpperCase() }</Typography.Text>
      </Space>

      <Form
        form={ form }
        layout='vertical'
        onValuesChange={ (_, allValues) => {
          const mergedTranslations = {
            ...(data?.translations ?? {}),
            ...(allValues?.translations ?? {})
          }
          const topName = mergedTranslations?.[currentLocale]?.name
          onChange({ ...allValues, translations: mergedTranslations, name: topName })
        } }
      >
        <LocalizedFieldsProvider locales={ [currentLocale] }>
          <Form.Item label={`Name (${currentLocale.toUpperCase()})`} name={ ['translations', currentLocale, 'name'] } rules={ [{ required: true }] }>
            <Input placeholder='Tax rate name' />
          </Form.Item>
        </LocalizedFieldsProvider>

        <Form.Item label='Rate (%)' name='rate' rules={ [{ required: true }] }>
          <InputNumber
            min={0}
            max={100}
            step={0.01}
            placeholder='Tax rate percentage'
            style={{ width: '100%' }}
            addonAfter='%'
          />
        </Form.Item>

        {renderEntityFormExtensions('coreshop.taxation.tax_rate.form', { data, onChange, currentLocale, form })}

        <Form.Item label='Active' name='active' valuePropName='checked'>
          <Switch />
        </Form.Item>
      </Form>
    </div>
  )
}