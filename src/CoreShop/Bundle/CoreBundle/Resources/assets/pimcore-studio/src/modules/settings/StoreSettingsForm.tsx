/**
 * CoreShop CoreBundle Studio Plugin
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
import { Collapse, Form, Input, InputNumber, Checkbox, Select, Space, Tag } from 'antd'
import { useTranslation } from 'react-i18next'

interface StoreSettingsFormProps {
  storeId: string
  values: Record<string, any>
  onChange: (key: string, value: any) => void
}

/**
 * TagInput - A simple tag-style input for entering multiple numeric values
 * Used for per-page options
 */
const TagInput: React.FC<{
  value?: number[]
  onChange?: (values: number[]) => void
}> = ({ value = [], onChange }) => {
  const [inputValue, setInputValue] = React.useState('')

  const handleAdd = () => {
    const num = parseInt(inputValue, 10)
    if (!isNaN(num) && num > 0 && !value.includes(num)) {
      onChange?.([...value, num].sort((a, b) => a - b))
    }
    setInputValue('')
  }

  const handleRemove = (removed: number) => {
    onChange?.(value.filter(v => v !== removed))
  }

  return (
    <Space direction="vertical" style={{ width: '100%' }}>
      <Space wrap>
        {value.map(v => (
          <Tag key={v} closable onClose={() => handleRemove(v)}>
            {v}
          </Tag>
        ))}
      </Space>
      <Input
        size="small"
        style={{ width: 120 }}
        value={inputValue}
        onChange={e => setInputValue(e.target.value)}
        onPressEnter={handleAdd}
        placeholder="Add value..."
        type="number"
      />
    </Space>
  )
}

/**
 * StoreSettingsForm - Form for a single store's configuration
 */
export const StoreSettingsForm: React.FC<StoreSettingsFormProps> = ({
  storeId,
  values,
  onChange
}) => {
  const { t } = useTranslation()

  const getValue = (key: string, defaultValue: any = '') => {
    return values[key] ?? defaultValue
  }

  const collapseItems = [
    {
      key: 'base',
      label: t('coreshop_base', { defaultValue: 'Base' }),
      children: (
        <Form layout="vertical">
          <Form.Item label={t('coreshop_guestcheckout', { defaultValue: 'Guest Checkout' })}>
            <Checkbox
              checked={!!getValue('system.guest.checkout')}
              onChange={e => onChange('system.guest.checkout', e.target.checked)}
            />
          </Form.Item>
        </Form>
      )
    },
    {
      key: 'category',
      label: t('coreshop_category', { defaultValue: 'Category' }),
      children: (
        <Form layout="vertical">
          <Form.Item label={t('coreshop_category_list_mode', { defaultValue: 'List Mode' })}>
            <Select
              value={getValue('system.category.list.mode', 'list')}
              onChange={v => onChange('system.category.list.mode', v)}
              options={[
                { value: 'list', label: t('coreshop_category_list_mode_list', { defaultValue: 'List' }) },
                { value: 'grid', label: t('coreshop_category_list_mode_grid', { defaultValue: 'Grid' }) }
              ]}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_category_list_per_page', { defaultValue: 'List Per Page Options' })}>
            <TagInput
              value={getValue('system.category.list.per_page', [])}
              onChange={v => onChange('system.category.list.per_page', v)}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_category_list_per_page_default', { defaultValue: 'Default Per Page (List)' })}>
            <InputNumber
              value={getValue('system.category.list.per_page.default')}
              onChange={v => onChange('system.category.list.per_page.default', v)}
              min={1}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_category_list_include_subcategories', { defaultValue: 'Include Subcategories' })}>
            <Checkbox
              checked={!!getValue('system.category.list.include_subcategories')}
              onChange={e => onChange('system.category.list.include_subcategories', e.target.checked)}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_category_grid_per_page', { defaultValue: 'Grid Per Page Options' })}>
            <TagInput
              value={getValue('system.category.grid.per_page', [])}
              onChange={v => onChange('system.category.grid.per_page', v)}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_category_grid_per_page_default', { defaultValue: 'Default Per Page (Grid)' })}>
            <InputNumber
              value={getValue('system.category.grid.per_page.default')}
              onChange={v => onChange('system.category.grid.per_page.default', v)}
              min={1}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_category_variant_mode', { defaultValue: 'Variant Mode' })}>
            <Select
              value={getValue('system.category.variant_mode', 'hide')}
              onChange={v => onChange('system.category.variant_mode', v)}
              options={[
                { value: 'hide', label: t('coreshop_category_variant_mode_hide', { defaultValue: 'Hide' }) },
                { value: 'include', label: t('coreshop_category_variant_mode_include', { defaultValue: 'Include' }) },
                { value: 'include_parent_object', label: t('coreshop_category_variant_mode_include_parent_object', { defaultValue: 'Include Parent Object' }) }
              ]}
              style={{ width: 300 }}
            />
          </Form.Item>
        </Form>
      )
    },
    {
      key: 'quote',
      label: t('coreshop_quote', { defaultValue: 'Quote' }),
      children: (
        <Form layout="vertical">
          <Form.Item label={t('coreshop_prefix', { defaultValue: 'Prefix' })}>
            <Input
              value={getValue('system.quote.prefix')}
              onChange={e => onChange('system.quote.prefix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_suffix', { defaultValue: 'Suffix' })}>
            <Input
              value={getValue('system.quote.suffix')}
              onChange={e => onChange('system.quote.suffix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>
        </Form>
      )
    },
    {
      key: 'order',
      label: t('coreshop_order', { defaultValue: 'Order' }),
      children: (
        <Form layout="vertical">
          <Form.Item label={t('coreshop_prefix', { defaultValue: 'Prefix' })}>
            <Input
              value={getValue('system.order.prefix')}
              onChange={e => onChange('system.order.prefix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_suffix', { defaultValue: 'Suffix' })}>
            <Input
              value={getValue('system.order.suffix')}
              onChange={e => onChange('system.order.suffix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>
        </Form>
      )
    },
    {
      key: 'invoice',
      label: t('coreshop_invoice', { defaultValue: 'Invoice' }),
      children: (
        <Form layout="vertical">
          <Form.Item label={t('coreshop_prefix', { defaultValue: 'Prefix' })}>
            <Input
              value={getValue('system.invoice.prefix')}
              onChange={e => onChange('system.invoice.prefix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_suffix', { defaultValue: 'Suffix' })}>
            <Input
              value={getValue('system.invoice.suffix')}
              onChange={e => onChange('system.invoice.suffix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_wkhtmltopdf_params', { defaultValue: 'wkhtmltopdf Params' })}>
            <Input
              value={getValue('system.invoice.wkhtml')}
              onChange={e => onChange('system.invoice.wkhtml', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>
        </Form>
      )
    },
    {
      key: 'shipping',
      label: t('coreshop_shipping', { defaultValue: 'Shipping' }),
      children: (
        <Form layout="vertical">
          <Form.Item label={t('coreshop_prefix', { defaultValue: 'Prefix' })}>
            <Input
              value={getValue('system.shipment.prefix')}
              onChange={e => onChange('system.shipment.prefix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_suffix', { defaultValue: 'Suffix' })}>
            <Input
              value={getValue('system.shipment.suffix')}
              onChange={e => onChange('system.shipment.suffix', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>

          <Form.Item label={t('coreshop_wkhtmltopdf_params', { defaultValue: 'wkhtmltopdf Params' })}>
            <Input
              value={getValue('system.shipment.wkhtml')}
              onChange={e => onChange('system.shipment.wkhtml', e.target.value)}
              style={{ width: 300 }}
            />
          </Form.Item>
        </Form>
      )
    }
  ]

  return (
    <div style={{ padding: '0 8px' }}>
      <Collapse
        items={collapseItems}
        defaultActiveKey={['base']}
      />
    </div>
  )
}
