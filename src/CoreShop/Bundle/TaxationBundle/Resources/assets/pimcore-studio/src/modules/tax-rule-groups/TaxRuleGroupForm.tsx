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
import { container } from '@pimcore/studio-ui-bundle'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { DynamicForm, type FormBuilder } from '@coreshop/studio-form/src/form-builder'
import type { TaxRuleGroupDetail, TaxRule } from './api'
import { Space, Typography, Table, Button, Select, Popconfirm } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { taxRateApi } from '../tax-rates/api'
import { getEntityTableColumnExtensions, getErrorMessage } from '@coreshop/resource/src/entities'

export interface TaxRuleGroupFormProps {
  data?: TaxRuleGroupDetail
  onChange: (draft: Partial<TaxRuleGroupDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const TaxRuleGroupForm: React.FC<TaxRuleGroupFormProps> = ({
  data,
  onChange,
  currentLocale,
  locales
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const builder = container.get<FormBuilder<TaxRuleGroupDetail>>('CoreShop/Taxation/TaxRuleGroup/FormBuilder')
  const config = React.useMemo(() => builder.build({ data, locale: currentLocale, locales }), [builder, data, currentLocale, locales])
  const [taxRates, setTaxRates] = React.useState<Array<{ id: number, name: string }>>([])

  const BEHAVIORS = [
    { value: 0, label: t('coreshop_tax_rule_behavior_disable', { defaultValue: 'This Tax only' }) },
    { value: 1, label: t('coreshop_tax_rule_behavior_combine', { defaultValue: 'Combine' }) },
    { value: 2, label: t('coreshop_tax_rule_behavior_on_after_another', { defaultValue: 'One after another' }) }
  ]

  // Load tax rates for the dropdown
  React.useEffect(() => {
    const loadTaxRates = async () => {
      try {
        const response = await taxRateApi.list()
        setTaxRates(response || [])
      } catch (error) {
        void messageApi.error(getErrorMessage(error, 'Failed to load tax rates'))
        setTaxRates([])
      }
    }
    void loadTaxRates()
  }, [])

  const handleTaxRulesChange = (newTaxRules: TaxRule[]) => {
    onChange({ ...data, taxRules: newTaxRules })
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
      <Space align="baseline" style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 16 }}>
        <Typography.Title level={5} style={{ margin: 0 }}>
          {t('coreshop_tax_rule_group', { defaultValue: 'Tax Rule Group' })}
        </Typography.Title>
      </Space>

      <DynamicForm config={config} data={data} onChange={onChange} currentLocale={currentLocale} />

      <div style={{ marginTop: 24 }}>
        <Space style={{ marginBottom: 16 }}>
          <Typography.Title level={5} style={{ margin: 0 }}>Tax Rules</Typography.Title>
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
