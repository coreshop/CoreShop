/**
 * CoreShop ShippingBundle - Dimension Condition
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const DimensionCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
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
        label="Width"
        name="width"
        help="Width in cm"
        rules={[{ required: true, message: 'Width is required' }]}
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={2}
          style={{ width: '100%' }}
          placeholder="Width"
          addonAfter="cm"
        />
      </Form.Item>

      <Form.Item
        label="Height"
        name="height"
        help="Height in cm"
        rules={[{ required: true, message: 'Height is required' }]}
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={2}
          style={{ width: '100%' }}
          placeholder="Height"
          addonAfter="cm"
        />
      </Form.Item>

      <Form.Item
        label="Depth"
        name="depth"
        help="Depth in cm"
        rules={[{ required: true, message: 'Depth is required' }]}
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={2}
          style={{ width: '100%' }}
          placeholder="Depth"
          addonAfter="cm"
        />
      </Form.Item>
    </Form>
  )
}
