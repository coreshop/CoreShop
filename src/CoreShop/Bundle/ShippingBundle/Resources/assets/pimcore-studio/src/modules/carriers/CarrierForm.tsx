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
import { container } from '@pimcore/studio-ui-bundle'
import { DynamicForm, type FormBuilder } from '@coreshop/studio-form/src/form-builder'
import { Tabs, Table, Button, InputNumber, Switch, Popconfirm } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import type { CarrierDetail, CarrierConfig, ShippingRuleAssignment } from './api'
import { ShippingRuleSelect } from '../../components/ShippingRuleSelect'

export interface CarrierFormProps {
  data?: CarrierDetail
  config: CarrierConfig
  onChange: (draft: Partial<CarrierDetail>) => void
  currentLocale: string
  locales?: string[]
}

export const CarrierForm: React.FC<CarrierFormProps> = ({
  data,
  config,
  onChange,
  currentLocale,
  locales
}) => {
  const builder = container.get<FormBuilder<CarrierDetail>>('CoreShop/Shipping/Carrier/FormBuilder')
  const formConfig = React.useMemo(() => builder.build({ data, locale: currentLocale, locales }), [builder, data, currentLocale, locales])
  const [shippingRules, setShippingRules] = React.useState<ShippingRuleAssignment[]>(
    data?.shippingRules ?? []
  )

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
      <DynamicForm config={formConfig} data={data} onChange={onChange} currentLocale={currentLocale} />
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
