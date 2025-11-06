/**
 * CoreShop ShippingBundle - Shipping Rule Condition
 */

import React from 'react'
import { Form } from 'antd'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'
import { ShippingRuleSelect } from '../../../components/ShippingRuleSelect'

export const ShippingRuleCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
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
        help="Check if another shipping rule is valid"
        rules={[{ required: true, message: 'Shipping rule is required' }]}
      >
        <ShippingRuleSelect style={{ width: '100%' }} />
      </Form.Item>
    </Form>
  )
}
