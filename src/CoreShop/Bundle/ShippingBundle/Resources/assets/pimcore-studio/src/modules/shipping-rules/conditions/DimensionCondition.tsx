/**
 * CoreShop ShippingBundle - Dimension Condition
 */

import React from 'react'
import { Form, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'

export const DimensionCondition: React.FC<ConditionComponentProps> = ({ data, onChange }) => {
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
        label={t('coreshop_condition_dimension_width', { defaultValue: 'Width' })}
        name="width"
        rules={[{ required: true, message: 'Width is required' }]}
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={2}
          style={{ width: '100%' }}
          addonAfter="cm"
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_condition_dimension_height', { defaultValue: 'Height' })}
        name="height"
        rules={[{ required: true, message: 'Height is required' }]}
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={2}
          style={{ width: '100%' }}
          addonAfter="cm"
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_condition_dimension_depth', { defaultValue: 'Depth' })}
        name="depth"
        rules={[{ required: true, message: 'Depth is required' }]}
      >
        <InputNumber
          min={0}
          step={0.1}
          precision={2}
          style={{ width: '100%' }}
          addonAfter="cm"
        />
      </Form.Item>
    </Form>
  )
}
