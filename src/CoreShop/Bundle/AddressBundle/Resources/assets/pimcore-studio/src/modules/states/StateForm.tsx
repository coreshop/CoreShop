import React from 'react'
import { Form, Input, Switch, Space, Typography } from 'antd'
import { useTranslation } from 'react-i18next'
import type { StateDetail } from './api'
import { LocalizedFieldsProvider } from '@coreshop/resource/src/components/localization/localized-fields'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'
import {CountrySelect} from "../../components/CountrySelect";

export const StateForm: React.FC<{
  data?: StateDetail
  onChange: (draft: Partial<StateDetail>) => void
  currentLocale: string
}> = ({ data, onChange, currentLocale }) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()

  React.useEffect(() => {
    const initial: any = { ...(data ?? {}) }
    if (typeof initial.active === 'undefined') initial.active = false
    initial.translations = initial.translations ?? {}
    if (!initial.translations[currentLocale]) {
      initial.translations[currentLocale] = { locale: currentLocale, name: data?.name ?? '' }
    }
    form.setFieldsValue(initial)
  }, [data, currentLocale])

  return (
    <div style={{ padding: 12 }}>
      <Space align='baseline' style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
        <Typography.Text type='secondary'>Translations</Typography.Text>
        <Typography.Text type='secondary'>{ currentLocale.toUpperCase() }</Typography.Text>
      </Space>

      <Form
        form={ form }
        layout='vertical'
        onValuesChange={ (_, allValues) => {
          const mergedTranslations = {
            ...(data?.translations ?? {}),
            ...(allValues?.translations ?? {})
          }
          const topName = mergedTranslations?.[currentLocale]?.name
          onChange({ ...allValues, translations: mergedTranslations, name: topName })
        } }
      >
        <LocalizedFieldsProvider locales={ [currentLocale] }>
          <Form.Item label={`Name (${currentLocale.toUpperCase()})`} name={ ['translations', currentLocale, 'name'] } rules={ [{ required: true }] }>
            <Input />
          </Form.Item>
        </LocalizedFieldsProvider>

        <Form.Item label={t('coreshop_state_isoCode', { defaultValue: 'ISO Code' })} name='isoCode'>
          <Input />
        </Form.Item>

        <Form.Item label={t('coreshop_state_country', { defaultValue: 'Country' })} name='country'>
          <CountrySelect />
        </Form.Item>

        {renderEntityFormExtensions('coreshop.address.state.form', { data, onChange, currentLocale, form })}

        <Form.Item label='Active' name='active' valuePropName='checked'>
          <Switch />
        </Form.Item>
      </Form>
    </div>
  )
}
