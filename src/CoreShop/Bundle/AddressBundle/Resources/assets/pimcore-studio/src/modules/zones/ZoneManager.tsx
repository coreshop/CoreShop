import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { zoneApi, type ZoneDetail } from './api'
import { ZoneForm } from './ZoneForm'

export const ZoneManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager<ZoneDetail>
      api={ zoneApi }
      dragType='coreshop:zone'
      leftRootTitle='Zones'
      buildSavePayload={ (data) => ({ id: data.id, name: data.name, active: data.active }) }
      onAdd={ async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add Zone',
          label: 'Name',
          onOk: async (value: string) => {
            const res = await zoneApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      }) }
      renderDetail={ (data, setData) => {
        if (!data) {
          return <div style={ { padding: 12, color: 'var(--ant-color-text-tertiary)' } }>Select a zone to view details.</div>
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
