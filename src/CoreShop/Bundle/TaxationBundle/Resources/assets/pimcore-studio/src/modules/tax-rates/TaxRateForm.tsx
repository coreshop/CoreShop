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
import { Input, InputNumber, Switch, Tag } from 'antd'
import { GlobalOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { TaxRateDetail } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export interface TaxRateFormProps {
  data?: TaxRateDetail
  onChange: (draft: Partial<TaxRateDetail>) => void
  currentLocale?: string
  locales?: string[]
}

// Helper to render localized field labels with visual indicator
const LocalizedLabel: React.FC<{ label: string, locale: string }> = ({ label, locale }) => (
    <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
        <GlobalOutlined style={{ color: 'var(--ant-color-primary)', fontSize: 12 }} />
        {label}
        <Tag color="blue" style={{ marginLeft: 4, fontSize: 10, lineHeight: '16px', padding: '0 4px' }}>
            {locale.toUpperCase()}
        </Tag>
    </span>
)

// Simple form field wrapper
const FormField: React.FC<{
    label: React.ReactNode
    required?: boolean
    children: React.ReactNode
}> = ({ label, required, children }) => (
    <div style={{ marginBottom: 16 }}>
        <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
            {label}
            {required && <span style={{ color: 'var(--ant-color-error)', marginLeft: 4 }}>*</span>}
        </label>
        {children}
    </div>
)

export const TaxRateForm: React.FC<TaxRateFormProps> = ({
  data,
  onChange,
  currentLocale = 'en',
  locales
}) => {
  const { t } = useTranslation()

  // Helper to get translation value
  const getTranslation = (field: 'name'): string => {
    return data?.translations?.[currentLocale]?.[field] ?? ''
  }

  // Helper to set translation value
  const setTranslation = (field: 'name', value: string) => {
    const currentTranslations = data?.translations ?? {}
    const currentLocaleTranslations = currentTranslations[currentLocale] ?? { locale: currentLocale }

    const updatedTranslations = {
      ...currentTranslations,
      [currentLocale]: {
        ...currentLocaleTranslations,
        [field]: value
      }
    }

    // Also update top-level name for backwards compatibility
    onChange({
      translations: updatedTranslations,
      name: value
    })
  }

  // Helper to update a simple field
  const updateField = <K extends keyof TaxRateDetail>(field: K, value: TaxRateDetail[K]) => {
    onChange({ [field]: value } as Partial<TaxRateDetail>)
  }

  return (
    <div style={{ padding: 12 }}>
      <FormField
        label={<LocalizedLabel label={t('coreshop_name', { defaultValue: 'Name' })} locale={currentLocale} />}
        required
      >
        <Input
          value={getTranslation('name')}
          onChange={(e) => setTranslation('name', e.target.value)}
        />
      </FormField>

      <FormField label={t('coreshop_tax_rate', { defaultValue: 'Tax Rate' })} required>
        <InputNumber
          min={0}
          max={100}
          step={0.01}
          style={{ width: '100%' }}
          addonAfter='%'
          value={data?.rate ?? 0}
          onChange={(value) => updateField('rate', value ?? 0)}
        />
      </FormField>

      {renderEntityFormExtensions('coreshop.taxation.tax_rate.form', { data, onChange, currentLocale })}

      <FormField label={t('coreshop_active', { defaultValue: 'Active' })}>
        <Switch
          checked={data?.active ?? false}
          onChange={(checked) => updateField('active', checked)}
        />
      </FormField>
    </div>
  )
}
