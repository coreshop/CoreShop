import React from 'react'
import { Form, Input, Select, Switch, Space, Typography } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { LocalizedFieldsProvider } from '@coreshop/resource/src/components/localization/localized-fields'
import type { CountryDetail } from './api'
import { renderEntityFormExtensions } from '@coreshop/resource/src/entities'

export const CountryForm: React.FC<{
  data?: CountryDetail
  zones: Array<{ id: number, name: string }>
  onChange: (draft: Partial<CountryDetail>) => void
  currentLocale: string
}> = ({ data, zones, onChange, currentLocale }) => {
  const [form] = Form.useForm()
  // locales + currentLocale provided by parent toolbar

  React.useEffect(() => {
    // Seed all known data so extensions get their initial values as well
    const initial: any = { ...(data ?? {}) }

    // sensible defaults without overwriting existing values
    if (typeof initial.active === 'undefined') initial.active = false
    if (!Array.isArray(initial.salutations)) initial.salutations = Array.isArray(data?.salutations) ? data?.salutations : []

    // ensure translations structure for current locale
    initial.translations = initial.translations ?? {}
    if (!initial.translations[currentLocale]) {
      initial.translations[currentLocale] = { locale: currentLocale, name: data?.name ?? '' }
    }

    form.setFieldsValue(initial)
  }, [data, currentLocale])

  return (
    <div style={ { padding: 12 } }>
      <Space align='baseline' style={ { display: 'flex', justifyContent: 'space-between', marginBottom: 8 } }>
        <Typography.Text type='secondary'>Translations</Typography.Text>
        <Typography.Text type='secondary'>{ currentLocale.toUpperCase() }</Typography.Text>
      </Space>

      <Form
        form={ form }
        layout='vertical'
        onValuesChange={ (_, allValues) => {
          // Preserve existing translations and only merge the edited ones
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
            <Input placeholder='Country name' />
          </Form.Item>
        </LocalizedFieldsProvider>

        <Form.Item label='ISO Code' name='isoCode'>
          <Input placeholder='ISO code (optional)' />
        </Form.Item>

        <Form.Item label='Zone'>
          <DroppableEntity
            accept='coreshop:zone'
            isValidData={ (info) => typeof info?.data?.id === 'number' }
            onDrop={ (info) => {
              const id = info?.data?.id
              if (typeof id === 'number') {
                form.setFieldsValue({ zone: id })
                onChange({ zone: id })
              }
            } }
          >
            <Form.Item name='zone' noStyle>
              <Select
                options={ zones.map(z => ({ value: z.id, label: z.name })) }
                placeholder='Select or drop a zone'
              />
            </Form.Item>
          </DroppableEntity>
        </Form.Item>

        <Form.Item label='Address Format' name='addressFormat'>
          <Input.TextArea autoSize={ { minRows: 6, maxRows: 16 } } placeholder='Address format template' />
        </Form.Item>

        <Form.Item label='Salutations' name='salutations'>
          <Select mode='tags' placeholder='Add salutations (e.g., mr, mrs)' />
        </Form.Item>

        {/* Extension slot: CoreBundle and others can inject extra fields */}
        {renderEntityFormExtensions('coreshop.address.country.form', { data, onChange, currentLocale, form })}

        {/* Additional locale names can be added later via add-locale UI */}

        <Form.Item label='Active' name='active' valuePropName='checked'>
          <Switch />
        </Form.Item>
      </Form>
    </div>
  )
}
