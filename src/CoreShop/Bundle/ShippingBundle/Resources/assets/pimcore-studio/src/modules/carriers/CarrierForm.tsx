/**
 * CoreShop ShippingBundle Studio Plugin
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
import { Form, Input, Select, Switch, Tabs, Space, Typography, Table, Button, InputNumber, Popconfirm } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import { LocalizedFieldsProvider } from '@coreshop/resource/src/components/localization/localized-fields'
import type { CarrierDetail, CarrierConfig, ShippingRuleAssignment } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'
import { ShippingRuleSelect } from '../../components/ShippingRuleSelect'
import { AssetSelect } from '@coreshop/pimcore/src/components/AssetSelect'

export const CarrierForm: React.FC<{
  data?: CarrierDetail
  config: CarrierConfig
  onChange: (draft: Partial<CarrierDetail>) => void
  currentLocale: string
}> = ({ data, config, onChange, currentLocale }) => {
  const [form] = Form.useForm()
  const [shippingRules, setShippingRules] = React.useState<ShippingRuleAssignment[]>(
    data?.shippingRules ?? []
  )

  React.useEffect(() => {
    const initial: any = { ...(data ?? {}) }

    // Defaults
    if (typeof initial.hideFromCheckout === 'undefined') initial.hideFromCheckout = false

    // Ensure translations structure
    initial.translations = initial.translations ?? {}
    if (!initial.translations[currentLocale]) {
      initial.translations[currentLocale] = {
        locale: currentLocale,
        title: data?.name ?? '',
        description: ''
      }
    }

    form.setFieldsValue(initial)
  }, [data, currentLocale])

  React.useEffect(() => {
    setShippingRules(data?.shippingRules ?? [])
  }, [data?.shippingRules])

  const handleShippingRulesChange = (updatedRules: ShippingRuleAssignment[]) => {
    setShippingRules(updatedRules)
    onChange({ shippingRules: updatedRules })
  }

  const addShippingRule = () => {
    const newRule: ShippingRuleAssignment = {
      shippingRule: 0,
      priority: 100,
      stopPropagation: false
    }
    handleShippingRulesChange([...shippingRules, newRule])
  }

  const deleteShippingRule = (index: number) => {
    const updated = shippingRules.filter((_, i) => i !== index)
    handleShippingRulesChange(updated)
  }

  const updateShippingRule = (index: number, field: keyof ShippingRuleAssignment, value: any) => {
    const updated = [...shippingRules]
    updated[index] = { ...updated[index], [field]: value }
    handleShippingRulesChange(updated)
  }

  const shippingRuleColumns = [
    {
      title: 'Shipping Rule',
      dataIndex: 'shippingRule',
      key: 'shippingRule',
      render: (value: number, record: ShippingRuleAssignment, index: number) => (
        <ShippingRuleSelect
          value={value}
          onChange={(newValue) => updateShippingRule(index, 'shippingRule', newValue ?? 0)}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: 'Priority',
      dataIndex: 'priority',
      key: 'priority',
      width: 150,
      render: (value: number, record: ShippingRuleAssignment, index: number) => (
        <InputNumber
          value={value}
          onChange={(newValue) => updateShippingRule(index, 'priority', newValue ?? 0)}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: 'Stop Propagation',
      dataIndex: 'stopPropagation',
      key: 'stopPropagation',
      width: 150,
      render: (value: boolean, record: ShippingRuleAssignment, index: number) => (
        <Switch
          checked={value}
          onChange={(checked) => {
            // If checked, uncheck all others
            if (checked) {
              const updated = shippingRules.map((rule, i) => ({
                ...rule,
                stopPropagation: i === index
              }))
              handleShippingRulesChange(updated)
            } else {
              updateShippingRule(index, 'stopPropagation', false)
            }
          }}
        />
      )
    },
    {
      title: '',
      key: 'actions',
      width: 80,
      render: (_: any, record: ShippingRuleAssignment, index: number) => (
        <Popconfirm
          title="Delete this shipping rule?"
          onConfirm={() => deleteShippingRule(index)}
          okText="Yes"
          cancelText="No"
        >
          <Button type="text" danger icon={<DeleteOutlined />} />
        </Popconfirm>
      )
    }
  ]

  const settingsTab = (
    <div style={{ padding: 24 }}>
      <Space align='baseline' style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
        <Typography.Text type='secondary'>Translations</Typography.Text>
        <Typography.Text type='secondary'>{currentLocale.toUpperCase()}</Typography.Text>
      </Space>

      <Form
        form={form}
        layout='vertical'
        onValuesChange={(_, allValues) => {
          const mergedTranslations = {
            ...(data?.translations ?? {}),
            ...(allValues?.translations ?? {})
          }
          const topTitle = mergedTranslations?.[currentLocale]?.title
          onChange({
            ...allValues,
            translations: mergedTranslations,
            name: topTitle
          })
        }}
      >
        {/* Basic Fields */}
        <Form.Item label='Identifier' name='identifier' rules={[{ required: true }]}>
          <Input placeholder='Carrier identifier' />
        </Form.Item>

        <Form.Item label='Tracking URL' name='trackingUrl'>
          <Input placeholder='e.g., https://tracking.example.com/{tracking_code}' />
        </Form.Item>

        <Form.Item label='Logo' name='logo'>
          <AssetSelect accept={['asset', 'asset:image']} placeholder='Drop image asset here or enter ID' />
        </Form.Item>

        {/* Translations */}
        <LocalizedFieldsProvider locales={[currentLocale]}>
          <Form.Item
            label={`Title (${currentLocale.toUpperCase()})`}
            name={['translations', currentLocale, 'title']}
            rules={[{ required: true }]}
          >
            <Input placeholder='Carrier title' />
          </Form.Item>

          <Form.Item
            label={`Description (${currentLocale.toUpperCase()})`}
            name={['translations', currentLocale, 'description']}
          >
            <Input.TextArea rows={3} placeholder='Carrier description' />
          </Form.Item>
        </LocalizedFieldsProvider>

        {/* Tax Calculation Strategy */}
        <Form.Item label='Tax Calculation Strategy' name='taxCalculationStrategy'>
          <Select
            placeholder='Select strategy'
            options={config.taxCalculationStrategies.map(s => ({
              value: s.value,
              label: s.label
            }))}
          />
        </Form.Item>

        <Form.Item label='Hide From Checkout' name='hideFromCheckout' valuePropName='checked'>
          <Switch />
        </Form.Item>

        {/* Extension slot for CoreBundle (stores, taxRule) */}
        {renderEntityFormExtensions('coreshop.shipping.carrier.form', { data, onChange, currentLocale, form })}
      </Form>
    </div>
  )

  const shippingRulesTab = (
    <div style={{ padding: 24 }}>
      <Table
        dataSource={shippingRules}
        columns={shippingRuleColumns}
        rowKey={(_, index) => `rule-${index}`}
        pagination={false}
        size="small"
      />
      <Button
        type="dashed"
        onClick={addShippingRule}
        icon={<PlusOutlined />}
        style={{ width: '100%', marginTop: 8 }}
      >
        Add Shipping Rule
      </Button>
    </div>
  )

  return (
    <Tabs
      defaultActiveKey="settings"
      items={[
        {
          key: 'settings',
          label: 'Settings',
          children: settingsTab
        },
        {
          key: 'shipping-rules',
          label: 'Shipping Locations and Costs',
          children: shippingRulesTab
        }
      ]}
      style={{ flex: 1, overflow: 'auto' }}
      tabBarStyle={{ paddingLeft: 24, paddingRight: 24, marginBottom: 0 }}
    />
  )
}
