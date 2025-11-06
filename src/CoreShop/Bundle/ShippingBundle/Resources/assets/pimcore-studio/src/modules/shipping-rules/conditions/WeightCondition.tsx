/**
 * CoreShop ShippingBundle - Weight Condition
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const WeightCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
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
        label="Min Weight"
        name="minWeight"
        help="Minimum weight in kg"
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={5}
          style={{ width: '100%' }}
          placeholder="Min weight"
        />
      </Form.Item>

      <Form.Item
        label="Max Weight"
        name="maxWeight"
        help="Maximum weight in kg"
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={5}
          style={{ width: '100%' }}
          placeholder="Max weight"
        />
      </Form.Item>
    </Form>
  )
}
