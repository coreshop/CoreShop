/**
 * CoreShop ShippingBundle - Discount Amount Action
 */

import React from 'react'
import { Form, InputNumber, Select } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'

export const DiscountAmountAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
  const [form] = Form.useForm()
  const [currencies, setCurrencies] = React.useState<Array<{ value: number, label: string }>>([])

  React.useEffect(() => {
    form.setFieldsValue(data ?? {})
  }, [data])

  React.useEffect(() => {
    void loadCurrencies()
  }, [])

  const loadCurrencies = async () => {
    try {
      const list = await currencyApi.list()
      setCurrencies(list.map(c => ({
        value: c.id!,
        label: c.name ?? `#${c.id}`
      })))
    } catch (err) {
      console.error('Failed to load currencies:', err)
    }
  }

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange(allValues)
      }}
    >
      <Form.Item
        label="Amount"
        name="amount"
        help="Discount amount from shipping cost (in smallest currency unit, e.g., cents)"
        rules={[{ required: true, message: 'Amount is required' }]}
      >
        <InputNumber
          min={0}
          step={1}
          precision={0}
          style={{ width: '100%' }}
          placeholder="Amount"
        />
      </Form.Item>

      <Form.Item
        label="Currency"
        name="currency"
        help="Currency for the amount"
        rules={[{ required: true, message: 'Currency is required' }]}
      >
        <Select
          placeholder="Select currency"
          options={currencies}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
