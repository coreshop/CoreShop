/**
 * CoreShop ShippingBundle - Price Action
 */

import React from 'react'
import { Form, InputNumber, Select } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'
import { currencyApi } from '@coreshop/currency/src/modules/currencies/api'
import { getErrorMessage } from '@coreshop/resource/src/entities'

export const PriceAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
  const [form] = Form.useForm()
  const messageApi = useMessage()
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
      void messageApi.error(getErrorMessage(err, 'Failed to load currencies'))
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
        label="Price"
        name="price"
        help="Fixed shipping price (in smallest currency unit, e.g., cents)"
        rules={[{ required: true, message: 'Price is required' }]}
      >
        <InputNumber
          min={0}
          step={1}
          precision={0}
          style={{ width: '100%' }}
          placeholder="Price"
        />
      </Form.Item>

      <Form.Item
        label="Currency"
        name="currency"
        help="Currency for the price"
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
