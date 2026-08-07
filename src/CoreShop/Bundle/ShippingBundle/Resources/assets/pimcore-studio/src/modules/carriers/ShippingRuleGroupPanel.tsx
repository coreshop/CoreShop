/**
 * CoreShop ShippingBundle - Shipping Rule Group Panel
 *
 * Editable table for managing shipping rule group assignments on a carrier.
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
import { Table, Button, InputNumber, Switch, Popconfirm } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { ShippingRuleAssignment } from './api'
import { ShippingRuleSelect } from '../../components/ShippingRuleSelect'

export interface ShippingRuleGroupPanelProps {
  ruleGroups: ShippingRuleAssignment[]
  onChange: (ruleGroups: ShippingRuleAssignment[]) => void
}

export const ShippingRuleGroupPanel: React.FC<ShippingRuleGroupPanelProps> = ({
  ruleGroups,
  onChange,
}) => {
  const { t } = useTranslation()

  const handleAdd = () => {
    onChange([...ruleGroups, { shippingRule: 0, priority: 100, stopPropagation: false }])
  }

  const handleRemove = (index: number) => {
    onChange(ruleGroups.filter((_, i) => i !== index))
  }

  const handleUpdate = (index: number, field: keyof ShippingRuleAssignment, value: any) => {
    const updated = [...ruleGroups]
    updated[index] = { ...updated[index], [field]: value }

    if (field === 'stopPropagation' && value === true) {
      updated.forEach((group, i) => {
        if (i !== index) {
          updated[i] = { ...group, stopPropagation: false }
        }
      })
    }

    onChange(updated)
  }

  const columns = [
    {
      title: t('coreshop_shipping_rule', { defaultValue: 'Shipping Rule' }),
      dataIndex: 'shippingRule',
      key: 'shippingRule',
      render: (value: number, _: ShippingRuleAssignment, index: number) => (
        <ShippingRuleSelect
          value={value}
          onChange={(newValue) => handleUpdate(index, 'shippingRule', newValue ?? 0)}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: t('coreshop_priority', { defaultValue: 'Priority' }),
      dataIndex: 'priority',
      key: 'priority',
      width: 150,
      render: (value: number, _: ShippingRuleAssignment, index: number) => (
        <InputNumber
          value={value}
          onChange={(newValue) => handleUpdate(index, 'priority', newValue ?? 0)}
          min={0}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: t('coreshop_stop_propagation', { defaultValue: 'Stop Propagation' }),
      dataIndex: 'stopPropagation',
      key: 'stopPropagation',
      width: 150,
      render: (value: boolean, _: ShippingRuleAssignment, index: number) => (
        <Switch
          checked={value}
          onChange={(checked) => handleUpdate(index, 'stopPropagation', checked)}
        />
      )
    },
    {
      title: '',
      key: 'actions',
      width: 80,
      render: (_: any, __: ShippingRuleAssignment, index: number) => (
        <Popconfirm
          title={t('coreshop_delete_confirm', { defaultValue: 'Are you sure you want to delete this?' })}
          onConfirm={() => handleRemove(index)}
          okText={t('yes', { defaultValue: 'Yes' })}
          cancelText={t('no', { defaultValue: 'No' })}
        >
          <Button type="text" danger icon={<DeleteOutlined />} size="small" />
        </Popconfirm>
      )
    }
  ]

  return (
    <div>
      <Table
        dataSource={ruleGroups.map((group, index) => ({ ...group, key: index }))}
        columns={columns}
        pagination={false}
        size="small"
      />
      <Button
        type="dashed"
        onClick={handleAdd}
        icon={<PlusOutlined />}
        style={{ width: '100%', marginTop: 8 }}
      >
        {t('coreshop_add', { defaultValue: 'Add' })}
      </Button>
    </div>
  )
}
