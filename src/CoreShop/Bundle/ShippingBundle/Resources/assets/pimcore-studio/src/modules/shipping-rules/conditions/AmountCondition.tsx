/**
 * CoreShop ShippingBundle - Amount Condition
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const AmountCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
  const { t } = useTranslation()
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
        label={t('coreshop_condition_amount_minAmount', { defaultValue: 'Min Amount' })}
        name="minAmount"
      >
        <InputNumber
          min={0}
          step={1}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_condition_amount_maxAmount', { defaultValue: 'Max Amount' })}
        name="maxAmount"
      >
        <InputNumber
          min={0}
          step={1}
          precision={2}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
