import React from 'react'
import { Form, Input, InputNumber } from 'antd'
import type { CurrencyDetail } from './api'

export const CurrencyForm: React.FC<{
  data?: CurrencyDetail
  onChange: (draft: Partial<CurrencyDetail>) => void
}> = ({ data, onChange }) => {
  const [form] = Form.useForm()

  React.useEffect(() => {
    form.setFieldsValue({
      name: data?.name ?? '',
      isoCode: data?.isoCode ?? '',
      numericIsoCode: data?.numericIsoCode ?? undefined,
      symbol: data?.symbol ?? ''
    })
  }, [data])

  return (
    <div style={{ padding: 12 }}>
      <Form
        form={ form }
        layout='vertical'
        onValuesChange={ (_, all) => onChange(all) }
      >
        <Form.Item label='Name' name='name' rules={[{ required: true }]}>
          <Input placeholder='Currency name' />
        </Form.Item>
        <Form.Item label='ISO Code' name='isoCode'>
          <Input placeholder='ISO 4217 code (e.g., EUR)' />
        </Form.Item>
        <Form.Item label='Numeric ISO Code' name='numericIsoCode'>
          <InputNumber style={{ width: '100%' }} placeholder='Numeric ISO (e.g., 978)' />
        </Form.Item>
        <Form.Item label='Symbol' name='symbol'>
          <Input placeholder='Symbol (e.g., €)' />
        </Form.Item>
      </Form>
    </div>
  )
}

