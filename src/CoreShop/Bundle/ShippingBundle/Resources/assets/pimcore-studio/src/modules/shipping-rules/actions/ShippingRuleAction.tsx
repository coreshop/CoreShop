/**
 * CoreShop ShippingBundle - Shipping Rule Action
 */

import React from 'react'
import { Form } from 'antd'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'
import { ShippingRuleSelect } from '../../../components/ShippingRuleSelect'

export const ShippingRuleAction: React.FC<ActionComponentProps> = ({ data, onChange }) => {
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
        label="Shipping Rule"
        name="shippingRule"
        help="Trigger actions from another shipping rule"
        rules={[{ required: true, message: 'Shipping rule is required' }]}
      >
        <ShippingRuleSelect style={{ width: '100%' }} />
      </Form.Item>
    </Form>
  )
}
