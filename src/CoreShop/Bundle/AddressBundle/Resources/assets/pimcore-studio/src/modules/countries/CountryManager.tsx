import React from 'react'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { type CountryDetail, countryApi } from './api'
import { zoneApi } from '../zones/api'
import { CountryForm } from './CountryForm'
import { GroupedEntityTabbedManager } from '@coreshop/resource/src/entities/components/GroupedEntityTabbedManager'

// Tabs are managed via shared hook

export const CountryManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <GroupedEntityTabbedManager<CountryDetail>
      api={ countryApi }
      dragType='coreshop:country'
      localizable
      loadGroups={ async () => await zoneApi.list() as any }
      resolveGroupId={ (li, groups) => {
        const it: any = li
        if (typeof it.zone === 'number') return it.zone
        if (typeof it.zoneName === 'string') return groups.find(g => g.name === it.zoneName)?.id ?? null
        return null
      } }
      applyGroup={ (data, groupId) => ({ ...data, zone: groupId ?? undefined } as CountryDetail) }
      buildSavePayload={ (data) => data }
      onAdd={ async (groupId?: number) => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_country_add', { defaultValue: 'Add Country' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (value: string) => {
            const res = await countryApi.add({ name: value, ...(groupId ? { zone: groupId } : {}) })
            resolve(res.data.id)
          }
        })
      }) }
      renderDetail={ (data, setData, zones, ctx) => {
        if (!data) return <div style={ { padding: 12, color: 'var(--ant-color-text-tertiary)' } }>{t('coreshop_country_select', { defaultValue: 'Select a country to view details.' })}</div>
        return (
          <CountryForm
            data={ data }
            onChange={ (draft) => setData(draft) }
            currentLocale={ ctx?.currentLocale ?? 'en' }
            locales={ ctx?.locales }
          />
        )
      } }
    />
  )
}

// obsolete local detail view removed in favor of GroupedEntityTabbedManager renderDetail
