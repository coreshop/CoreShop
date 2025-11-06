/**
 * CoreShop ShippingBundle - Amount Condition
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const AmountCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
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
        label="Min Amount"
        name="minAmount"
        help="Minimum cart amount"
      >
        <InputNumber
          min={0}
          step={1}
          precision={2}
          style={{ width: '100%' }}
          placeholder="Min amount"
        />
      </Form.Item>

      <Form.Item
        label="Max Amount"
        name="maxAmount"
        help="Maximum cart amount"
      >
        <InputNumber
          min={0}
          step={1}
          precision={2}
          style={{ width: '100%' }}
          placeholder="Max amount"
        />
      </Form.Item>
    </Form>
  )
}
