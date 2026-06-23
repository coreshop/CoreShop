/**
 * CoreShop PaymentBundle Studio Plugin
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
import { Table, Button, InputNumber, Checkbox, Space, Popconfirm } from 'antd'
import { PlusOutlined, DeleteOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { PaymentProviderRuleGroup } from './api'
import { PaymentProviderRuleSelect } from '../payment-provider-rules/components/PaymentProviderRuleSelect'

export interface PaymentProviderRuleGroupPanelProps {
  ruleGroups: PaymentProviderRuleGroup[]
  onChange: (ruleGroups: PaymentProviderRuleGroup[]) => void
}

export const PaymentProviderRuleGroupPanel: React.FC<PaymentProviderRuleGroupPanelProps> = ({
  ruleGroups,
  onChange
}) => {
  const { t } = useTranslation()

  const handleAdd = () => {
    const newGroup: PaymentProviderRuleGroup = {
      priority: 100,
      stopPropagation: false,
      paymentProviderRule: undefined
    }
    onChange([...ruleGroups, newGroup])
  }

  const handleRemove = (index: number) => {
    const updated = [...ruleGroups]
    updated.splice(index, 1)
    onChange(updated)
  }

  const handleUpdate = (index: number, field: keyof PaymentProviderRuleGroup, value: any) => {
    const updated = [...ruleGroups]
    updated[index] = {
      ...updated[index],
      [field]: value
    }

    // If stopPropagation is enabled, disable it for all other rows
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
      title: t('coreshop_payment_provider_rule', { defaultValue: 'Payment Provider Rule' }),
      dataIndex: 'paymentProviderRule',
      key: 'paymentProviderRule',
      width: '40%',
      render: (value: number | undefined, _: PaymentProviderRuleGroup, index: number) => (
        <PaymentProviderRuleSelect
          value={value}
          onChange={(newValue) => handleUpdate(index, 'paymentProviderRule', newValue)}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: t('coreshop_priority', { defaultValue: 'Priority' }),
      dataIndex: 'priority',
      key: 'priority',
      width: '20%',
      render: (value: number, _: PaymentProviderRuleGroup, index: number) => (
        <InputNumber
          value={value}
          onChange={(newValue) => handleUpdate(index, 'priority', newValue ?? 100)}
          min={0}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: t('coreshop_stop_propagation', { defaultValue: 'Stop Propagation' }),
      dataIndex: 'stopPropagation',
      key: 'stopPropagation',
      width: '20%',
      render: (value: boolean, _: PaymentProviderRuleGroup, index: number) => (
        <Checkbox
          checked={value}
          onChange={(e) => handleUpdate(index, 'stopPropagation', e.target.checked)}
        />
      )
    },
    {
      title: '',
      key: 'actions',
      width: '10%',
      render: (_: any, __: PaymentProviderRuleGroup, index: number) => (
        <Popconfirm
          title={t('coreshop_delete_confirm', { defaultValue: 'Are you sure you want to delete this?' })}
          onConfirm={() => handleRemove(index)}
          okText={t('yes', { defaultValue: 'Yes' })}
          cancelText={t('no', { defaultValue: 'No' })}
        >
          <Button
            type="text"
            danger
            icon={<DeleteOutlined />}
            size="small"
          />
        </Popconfirm>
      )
    }
  ]

  return (
    <div>
      <Space style={{ marginBottom: 16 }}>
        <Button
          type="primary"
          icon={<PlusOutlined />}
          onClick={handleAdd}
        >
          {t('coreshop_add', { defaultValue: 'Add' })}
        </Button>
      </Space>

      <Table
        dataSource={ruleGroups.map((group, index) => ({ ...group, key: index }))}
        columns={columns}
        pagination={false}
        size="small"
      />
    </div>
  )
}
