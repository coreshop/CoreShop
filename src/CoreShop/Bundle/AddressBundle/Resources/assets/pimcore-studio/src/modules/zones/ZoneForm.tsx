import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { DynamicForm, type FormBuilder } from '@coreshop/studio-form/src/form-builder'
import type { ZoneDetail } from './api'
import { Space, Typography } from 'antd'
import { useTranslation } from 'react-i18next'

export interface ZoneFormProps {
  data?: ZoneDetail
  onChange: (draft: Partial<ZoneDetail>) => void
  currentLocale?: string
  locales?: string[]
}

export const ZoneForm: React.FC<ZoneFormProps> = ({
  data,
  onChange,
  currentLocale,
  locales
}) => {
  const { t } = useTranslation()
  const builder = container.get<FormBuilder<ZoneDetail>>('CoreShop/Address/Zone/FormBuilder')
  const config = React.useMemo(() => builder.build({ data, locale: currentLocale, locales }), [builder, data, currentLocale, locales])

  return (
    <div style={{ padding: 12 }}>
      <Space align="baseline" style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 16 }}>
        <Typography.Title level={5} style={{ margin: 0 }}>
          {t('coreshop_zone_configuration', { defaultValue: 'Zone Configuration' })}
        </Typography.Title>
      </Space>
      <DynamicForm config={config} data={data} onChange={onChange} currentLocale={currentLocale} />
    </div>
  )
}
