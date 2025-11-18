import React from 'react'
import { Form, Input, InputNumber } from 'antd'
import { useTranslation } from 'react-i18next'
import type { CurrencyDetail } from './api'

export const CurrencyForm: React.FC<{
  data?: CurrencyDetail
  onChange: (draft: Partial<CurrencyDetail>) => void
}> = ({ data, onChange }) => {
  const { t } = useTranslation()
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
        <Form.Item label={t('coreshop_currency', { defaultValue: 'Currency' })} name='name' rules={[{ required: true }]}>
          <Input />
        </Form.Item>
        <Form.Item label={t('coreshop_currency_isoCode', { defaultValue: 'ISO Code' })} name='isoCode'>
          <Input />
        </Form.Item>
        <Form.Item label={t('coreshop_currency_numericIsoCode', { defaultValue: 'Numeric ISO Code' })} name='numericIsoCode'>
          <InputNumber style={{ width: '100%' }} />
        </Form.Item>
        <Form.Item label={t('coreshop_currency_symbol', { defaultValue: 'Symbol' })} name='symbol'>
          <Input />
        </Form.Item>
      </Form>
    </div>
  )
}

