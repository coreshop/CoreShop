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
import { Form, Input, Switch, Table, Button, Select, Space, Popconfirm } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { TaxRuleGroupDetail, TaxRule } from './api'
import { taxRateApi } from '../tax-rates/api'
import { renderEntityFormExtensions, getEntityTableColumnExtensions } from '@coreshop/resource/src/entities'

export const TaxRuleGroupForm: React.FC<{
  data?: TaxRuleGroupDetail
  onChange: (draft: Partial<TaxRuleGroupDetail>) => void
  currentLocale: string
}> = ({ data, onChange, currentLocale }) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()
  const [taxRates, setTaxRates] = React.useState<Array<{ id: number, name: string }>>([])

  const BEHAVIORS = [
    { value: 0, label: t('coreshop_tax_rule_behavior_disable', { defaultValue: 'This Tax only' }) },
    { value: 1, label: t('coreshop_tax_rule_behavior_combine', { defaultValue: 'Combine' }) },
    { value: 2, label: t('coreshop_tax_rule_behavior_on_after_another', { defaultValue: 'One after another' }) }
  ]

  // Extension columns removed - will be handled differently

  // Load tax rates for the dropdown
  React.useEffect(() => {
    const loadTaxRates = async () => {
      try {
        const response = await taxRateApi.list()
        // API returns array directly: [{"name":"20AT","id":1},{"name":"10AT","id":2}]
        setTaxRates(response || [])
      } catch (error) {
        console.error('Failed to load tax rates:', error)
        setTaxRates([])
      }
    }
    void loadTaxRates()
  }, [])

  // Extension columns will be added via renderEntityFormExtensions registry
  // No direct dependency on CoreBundle

  React.useEffect(() => {
    const initial: any = { ...(data ?? {}) }
    if (typeof initial.active === 'undefined') initial.active = false
    if (!Array.isArray(initial.taxRules)) initial.taxRules = []
    
    form.setFieldsValue(initial)
  }, [data, form])

  const handleTaxRulesChange = (newTaxRules: TaxRule[]) => {
    const updatedData = { ...data, taxRules: newTaxRules }
    onChange(updatedData)
  }

  const addTaxRule = () => {
    const currentTaxRules = data?.taxRules || []
    const newTaxRule: TaxRule = {
      taxRuleGroup: data?.id || 0,
      taxRate: 0,
      behavior: 0,
      country: undefined,
      state: undefined
    }
    handleTaxRulesChange([...currentTaxRules, newTaxRule])
  }

  const removeTaxRule = (index: number) => {
    const currentTaxRules = data?.taxRules || []
    const newTaxRules = currentTaxRules.filter((_, i) => i !== index)
    handleTaxRulesChange(newTaxRules)
  }

  const updateTaxRule = (index: number, field: keyof TaxRule, value: any) => {
    const currentTaxRules = [...(data?.taxRules || [])]
    if (currentTaxRules[index]) {
      currentTaxRules[index] = { ...currentTaxRules[index], [field]: value }
      handleTaxRulesChange(currentTaxRules)
    }
  }

  const baseColumns = [
    {
      title: 'Tax Rate',
      dataIndex: 'taxRate',
      width: 200,
      render: (value: number, _: TaxRule, index: number) => (
        <Select
          value={value}
          onChange={(newValue) => updateTaxRule(index, 'taxRate', newValue)}
          options={taxRates.map(rate => ({ value: rate.id, label: rate.name }))}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: 'Behavior',
      dataIndex: 'behavior',
      width: 200,
      render: (value: number, _: TaxRule, index: number) => (
        <Select
          value={value}
          onChange={(newValue) => updateTaxRule(index, 'behavior', newValue)}
          options={BEHAVIORS}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: 'Actions',
      width: 80,
      render: (_: any, __: TaxRule, index: number) => (
        <Popconfirm
          title="Delete tax rule?"
          onConfirm={() => removeTaxRule(index)}
          okText="Yes"
          cancelText="No"
        >
          <Button type="text" icon={<DeleteOutlined />} danger />
        </Popconfirm>
      )
    }
  ]

  // Get extension columns from registry
  const extensionColumns = getEntityTableColumnExtensions(
    'coreshop.taxation.tax_rule_group.tax_rules',
    updateTaxRule
  )

  // Combine base columns with extension columns - Country and State first
  const columns = [
    ...extensionColumns, // Extension columns (Country, State, etc.) - FIRST
    ...baseColumns.slice(0, -1), // Tax Rate and Behavior columns
    baseColumns[baseColumns.length - 1] // Actions column at the end
  ]

  return (
    <div style={{ padding: 12 }}>
      <Form
        form={form}
        layout="vertical"
        onValuesChange={(_, allValues) => {
          onChange({ ...allValues, taxRules: data?.taxRules || [] })
        }}
      >
        <Form.Item label="Name" name="name" rules={[{ required: true }]}>
          <Input />
        </Form.Item>

        <Form.Item label="Active" name="active" valuePropName="checked">
          <Switch />
        </Form.Item>

        {renderEntityFormExtensions('coreshop.taxation.tax_rule_group.form', { data, onChange, currentLocale, form })}
      </Form>

      <div style={{ marginTop: 24 }}>
        <Space style={{ marginBottom: 16 }}>
          <h3>Tax Rules</h3>
          <Button 
            type="primary" 
            icon={<PlusOutlined />} 
            onClick={addTaxRule}
          >
            Add Tax Rule
          </Button>
        </Space>

        <Table
          columns={columns}
          dataSource={data?.taxRules || []}
          pagination={false}
          rowKey={(_, index) => index?.toString() || '0'}
          size="middle"
        />
      </div>
    </div>
  )
}