/**
 * CoreShop ShippingBundle - Postcodes Condition
 */

import React from 'react'
import { Form, Input } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const PostcodesCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
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
        label="Postcodes"
        name="postcodes"
        help="Enter postcodes separated by commas (e.g., 10115, 20095, 30159)"
        rules={[{ required: true, message: 'At least one postcode is required' }]}
      >
        <Input.TextArea
          rows={4}
          placeholder="Enter postcodes separated by commas"
        />
      </Form.Item>
    </Form>
  )
}
