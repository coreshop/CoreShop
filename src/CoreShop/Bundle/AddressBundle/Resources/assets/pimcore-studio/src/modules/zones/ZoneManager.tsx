import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { zoneApi, type ZoneDetail } from './api'
import { ZoneForm } from './ZoneForm'

export const ZoneManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <EntityTabbedManager<ZoneDetail>
      api={ zoneApi }
      dragType='coreshop:zone'
      leftRootTitle={t('coreshop_zones', { defaultValue: 'Zones' })}
      buildSavePayload={ (data) => data }
      onAdd={ async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_zone_add', { defaultValue: 'Add Zone' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          onOk: async (value: string) => {
            const res = await zoneApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      }) }
      renderDetail={ (data, setData) => {
        if (!data) {
          return <div style={ { padding: 12, color: 'var(--ant-color-text-tertiary)' } }>{t('coreshop_zone_select', { defaultValue: 'Select a zone to view details.' })}</div>
        }

        return (
          <ZoneForm
            data={ data }
            onChange={ (draft) => {
              setData(draft)
            } }
          />
        )
      } }
    />
  )
}
