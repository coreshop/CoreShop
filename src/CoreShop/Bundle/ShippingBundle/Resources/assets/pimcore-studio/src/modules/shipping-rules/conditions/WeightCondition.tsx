/**
 * CoreShop ShippingBundle - Weight Condition
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const WeightCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
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
        label={t('coreshop_condition_weight_minWeight', { defaultValue: 'Min Weight' })}
        name="minWeight"
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={5}
          style={{ width: '100%' }}
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_condition_weight_maxWeight', { defaultValue: 'Max Weight' })}
        name="maxWeight"
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={5}
          style={{ width: '100%' }}
        />
      </Form.Item>
    </Form>
  )
}
