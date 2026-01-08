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
import { Input, Typography, Tag } from 'antd'
import { GlobalOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ProductUnitDetail } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export interface ProductUnitFormProps {
  data?: ProductUnitDetail
  onChange: (draft: Partial<ProductUnitDetail>) => void
  currentLocale?: string
  locales?: string[]
}

// Helper to render localized field labels with visual indicator
const LocalizedLabel: React.FC<{ label: string, locale: string, tooltip?: string }> = ({ label, locale, tooltip }) => (
    <span style={{ display: 'flex', alignItems: 'center', gap: 6 }} title={tooltip}>
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
    tooltip?: string
    children: React.ReactNode
}> = ({ label, required, tooltip, children }) => (
    <div style={{ marginBottom: 16 }}>
        <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }} title={tooltip}>
            {label}
            {required && <span style={{ color: 'var(--ant-color-error)', marginLeft: 4 }}>*</span>}
        </label>
        {children}
    </div>
)

export const ProductUnitForm: React.FC<ProductUnitFormProps> = ({
  data,
  onChange,
  currentLocale = 'en',
  locales
}) => {
  const { t } = useTranslation()

  // Helper to update a field
  const updateField = <K extends keyof ProductUnitDetail>(field: K, value: ProductUnitDetail[K]) => {
    onChange({ [field]: value } as Partial<ProductUnitDetail>)
  }

  return (
    <div style={{ padding: 12 }}>
      <FormField
        label={t('coreshop_name', { defaultValue: 'Name' })}
        required
      >
        <Input
          placeholder={t('coreshop_product_unit_name_placeholder', { defaultValue: 'e.g., piece, kg, liter' })}
          value={data?.name ?? ''}
          onChange={(e) => updateField('name', e.target.value)}
        />
      </FormField>

      <Typography.Title level={5} style={{ marginTop: 24, marginBottom: 16 }}>
        {t('coreshop_translations', { defaultValue: 'Translations' })}
      </Typography.Title>

      <FormField
        label={<LocalizedLabel
          label={t('coreshop_product_unit_full_label', { defaultValue: 'Full Label (Singular)' })}
          locale={currentLocale}
          tooltip={t('coreshop_product_unit_full_label_tooltip', { defaultValue: 'Full singular form, e.g., "Piece"' })}
        />}
      >
        <Input
          placeholder={t('coreshop_product_unit_full_label_placeholder', { defaultValue: 'e.g., Piece' })}
          value={data?.fullLabel ?? ''}
          onChange={(e) => updateField('fullLabel', e.target.value)}
        />
      </FormField>

      <FormField
        label={<LocalizedLabel
          label={t('coreshop_product_unit_full_plural_label', { defaultValue: 'Full Label (Plural)' })}
          locale={currentLocale}
          tooltip={t('coreshop_product_unit_full_plural_label_tooltip', { defaultValue: 'Full plural form, e.g., "Pieces"' })}
        />}
      >
        <Input
          placeholder={t('coreshop_product_unit_full_plural_label_placeholder', { defaultValue: 'e.g., Pieces' })}
          value={data?.fullPluralLabel ?? ''}
          onChange={(e) => updateField('fullPluralLabel', e.target.value)}
        />
      </FormField>

      <FormField
        label={<LocalizedLabel
          label={t('coreshop_product_unit_short_label', { defaultValue: 'Short Label (Singular)' })}
          locale={currentLocale}
          tooltip={t('coreshop_product_unit_short_label_tooltip', { defaultValue: 'Abbreviated singular form, e.g., "pc"' })}
        />}
      >
        <Input
          placeholder={t('coreshop_product_unit_short_label_placeholder', { defaultValue: 'e.g., pc' })}
          value={data?.shortLabel ?? ''}
          onChange={(e) => updateField('shortLabel', e.target.value)}
        />
      </FormField>

      <FormField
        label={<LocalizedLabel
          label={t('coreshop_product_unit_short_plural_label', { defaultValue: 'Short Label (Plural)' })}
          locale={currentLocale}
          tooltip={t('coreshop_product_unit_short_plural_label_tooltip', { defaultValue: 'Abbreviated plural form, e.g., "pcs"' })}
        />}
      >
        <Input
          placeholder={t('coreshop_product_unit_short_plural_label_placeholder', { defaultValue: 'e.g., pcs' })}
          value={data?.shortPluralLabel ?? ''}
          onChange={(e) => updateField('shortPluralLabel', e.target.value)}
        />
      </FormField>

      {renderEntityFormExtensions('coreshop.product.product_unit.form', { data, onChange, currentLocale })}
    </div>
  )
}
