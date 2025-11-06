/**
 * CoreShop ShippingBundle - Shipping Rule Settings Form
 */

import React from 'react'
import { Form, Input, Checkbox } from 'antd'
import type { ShippingRuleDetail } from '../api'

interface SettingsFormProps {
  rule: ShippingRuleDetail
  onChange: (rule: ShippingRuleDetail) => void
  currentLocale: string
}

export const SettingsForm: React.FC<SettingsFormProps> = ({
  rule,
  onChange
}) => {
  const [form] = Form.useForm()

  React.useEffect(() => {
    const initial: any = { ...(rule ?? {}) }
    form.setFieldsValue(initial)
  }, [rule])

  return (
    <div style={{ padding: 12 }}>
      <Form
        form={form}
        layout="vertical"
        onValuesChange={(_, allValues) => {
          onChange(allValues)
        }}
      >
        <Form.Item label="Name" name="name" rules={[{ required: true }]}>
          <Input placeholder="Rule name" />
        </Form.Item>

        <Form.Item name="active" valuePropName="checked">
          <Checkbox>Active</Checkbox>
        </Form.Item>
      </Form>
    </div>
  )
}
