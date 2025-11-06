/**
 * CoreShop ShippingBundle - Discount Percent Action
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'

export const DiscountPercentAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
  const [form] = Form.useForm()

  React.useEffect(() => {
    form.setFieldsValue(data ?? {})
  }, [data])

  return (
    <Form
      form={form}
      layout="vertical"
      onValuesChange={(_, allValues) => {
        onChange(allValues)
      }}
    >
      <Form.Item
        label="Percent"
        name="percent"
        help="Percentage discount on shipping cost (0-100)"
        rules={[{ required: true, message: 'Percent is required' }]}
      >
        <InputNumber
          min={0}
          max={100}
          step={0.01}
          precision={2}
          style={{ width: '100%' }}
          placeholder="Percent"
          addonAfter="%"
        />
      </Form.Item>
    </Form>
  )
}
