import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useStudioLanguages } from '@coreshop/resource/src/components/localization/useStudioLanguages'
import { stateApi, type StateDetail } from './api'
import { StateForm } from './StateForm'
import { Select } from 'antd'

export const StateManager: React.FC = () => {
  const modal = useFormModal()
  const locales = useStudioLanguages()
  const [currentLocale, setCurrentLocale] = React.useState<string>(() => locales[0] ?? 'en')
  React.useEffect(() => {
    if (!locales.includes(currentLocale)) setCurrentLocale(locales[0])
  }, [locales])

  return (
    <EntityTabbedManager<StateDetail>
      api={ stateApi }
      dragType='coreshop:state'
      leftRootTitle='States'
      getTitle={ (li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}` }
      buildSavePayload={ (data) => ({
        id: data.id,
        name: data.name,
        active: data.active,
        isoCode: data.isoCode,
        country: data.country,
        translations: data.translations
      }) }
      onAdd={ async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add State',
          label: 'Name',
          onOk: async (value: string) => {
            const res = await stateApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      }) }
      leftExtras={ (
        <Select
          size='small'
          value={ currentLocale }
          options={ locales.map(l => ({ value: l, label: l.toUpperCase() })) }
          onChange={ setCurrentLocale }
        />
      ) }
      renderDetail={ (data, setData) => {
        if (!data) {
          return <div style={ { padding: 12, color: 'var(--ant-color-text-tertiary)' } }>Select a state to view details.</div>
        }

        return (
          <StateForm
            data={ data }
            currentLocale={ currentLocale }
            onChange={ (draft) => setData(draft) }
          />
        )
      } }
    />
  )
}
