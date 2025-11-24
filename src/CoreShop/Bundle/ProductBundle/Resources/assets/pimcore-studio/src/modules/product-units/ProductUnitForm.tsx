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
import { Form, Input, Space, Typography } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ProductUnitDetail } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export interface ProductUnitFormProps {
  data?: ProductUnitDetail
  onChange: (draft: Partial<ProductUnitDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const ProductUnitForm: React.FC<ProductUnitFormProps> = ({
  data,
  onChange,
  currentLocale = 'en',
  locales
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  React.useEffect(() => {
    form.setFieldsValue(data ?? {})
  }, [data, currentLocale, form])

  return (
    <div style={{ padding: 12 }}>
      <Space align='baseline' style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
        <Typography.Text type='secondary'>
          {t('coreshop_product_unit_configuration', { defaultValue: 'Product Unit Configuration' })}
        </Typography.Text>
        <Typography.Text type='secondary'>{currentLocale.toUpperCase()}</Typography.Text>
      </Space>

      <Form
        form={form}
        layout='vertical'
        onValuesChange={(_, allValues) => {
          onChange(allValues)
        }}
      >
        <Form.Item
          label={t('coreshop_name', { defaultValue: 'Name' })}
          name='name'
          rules={[{ required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) }]}
        >
          <Input placeholder={t('coreshop_product_unit_name_placeholder', { defaultValue: 'e.g., piece, kg, liter' })} />
        </Form.Item>

        <Typography.Title level={5} style={{ marginTop: 24, marginBottom: 16 }}>
          {t('coreshop_translations', { defaultValue: 'Translations' })} ({currentLocale.toUpperCase()})
        </Typography.Title>

        <Form.Item
          label={t('coreshop_product_unit_full_label', { defaultValue: 'Full Label (Singular)' })}
          name='fullLabel'
          tooltip={t('coreshop_product_unit_full_label_tooltip', { defaultValue: 'Full singular form, e.g., "Piece"' })}
        >
          <Input placeholder={t('coreshop_product_unit_full_label_placeholder', { defaultValue: 'e.g., Piece' })} />
        </Form.Item>

        <Form.Item
          label={t('coreshop_product_unit_full_plural_label', { defaultValue: 'Full Label (Plural)' })}
          name='fullPluralLabel'
          tooltip={t('coreshop_product_unit_full_plural_label_tooltip', { defaultValue: 'Full plural form, e.g., "Pieces"' })}
        >
          <Input placeholder={t('coreshop_product_unit_full_plural_label_placeholder', { defaultValue: 'e.g., Pieces' })} />
        </Form.Item>

        <Form.Item
          label={t('coreshop_product_unit_short_label', { defaultValue: 'Short Label (Singular)' })}
          name='shortLabel'
          tooltip={t('coreshop_product_unit_short_label_tooltip', { defaultValue: 'Abbreviated singular form, e.g., "pc"' })}
        >
          <Input placeholder={t('coreshop_product_unit_short_label_placeholder', { defaultValue: 'e.g., pc' })} />
        </Form.Item>

        <Form.Item
          label={t('coreshop_product_unit_short_plural_label', { defaultValue: 'Short Label (Plural)' })}
          name='shortPluralLabel'
          tooltip={t('coreshop_product_unit_short_plural_label_tooltip', { defaultValue: 'Abbreviated plural form, e.g., "pcs"' })}
        >
          <Input placeholder={t('coreshop_product_unit_short_plural_label_placeholder', { defaultValue: 'e.g., pcs' })} />
        </Form.Item>

        {renderEntityFormExtensions('coreshop.product.product_unit.form', { data, onChange, currentLocale, form })}
      </Form>
    </div>
  )
}
