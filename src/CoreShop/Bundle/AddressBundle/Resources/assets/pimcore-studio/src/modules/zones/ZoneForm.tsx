import React from 'react'
import { Form, Input, Switch } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ZoneDetail } from './api'

export const ZoneForm: React.FC<{ data?: ZoneDetail, onChange: (draft: Partial<ZoneDetail>) => void }>
  = ({ data, onChange }) => {
    const { t } = useTranslation()
    const [form] = Form.useForm()

    React.useEffect(() => {
      form.setFieldsValue({
        name: data?.name,
        active: data?.active ?? false
      })
    }, [data])

    return (
      <div style={ { padding: 12 } }>
        <Form
          form={ form }
          layout='vertical'
          onValuesChange={ (_, allValues) => onChange(allValues) }
        >
          <Form.Item label={t('coreshop_zone', { defaultValue: 'Zone' })} name='name' rules={ [{ required: true }] }>
            <Input />
          </Form.Item>

          <Form.Item label='Active' name='active' valuePropName='checked'>
            <Switch />
          </Form.Item>

          {/* Countries selection could be added later using a proper source */}
        </Form>
      </div>
    )
  }

