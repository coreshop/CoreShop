import React from 'react'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { type CountryDetail, countryApi } from './api'
import { zoneApi } from '../zones/api'
import { CountryForm } from './CountryForm'
import { getEntitySaveDecoratorRegistry } from '@coreshop/resource/src/entities/save-decorators'
import { GroupedEntityTabbedManager } from '@coreshop/resource/src/entities/components/GroupedEntityTabbedManager'

// Tabs are managed via shared hook

export const CountryManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()
  const buildSavePayload = React.useCallback((data: CountryDetail) => {
    const t: any = data
    const translations: Record<string, { name: string }> = {}
    const rawTranslations = (t.translations ?? {}) as Record<string, any>
    Object.keys(rawTranslations).forEach((locale) => {
      const entry = rawTranslations[locale] ?? {}
      translations[locale] = { name: entry?.name ?? '' }
    })
    const base = {
      id: t.id,
      name: t.name,
      translations,
      isoCode: t.isoCode,
      active: !!t.active,
      zone: t.zone,
      addressFormat: t.addressFormat,
      salutations: Array.isArray(t.salutations) ? t.salutations : [],
      currency: t.currency
    }
    const registry = getEntitySaveDecoratorRegistry()
    return registry?.apply('/coreshop/countries', base, data) ?? base
  }, [])

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
      buildSavePayload={ buildSavePayload }
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
            zones={ zones }
            onChange={ (draft) => setData(draft) }
            currentLocale={ ctx?.currentLocale ?? 'en' }
          />
        )
      } }
    />
  )
}

// obsolete local detail view removed in favor of GroupedEntityTabbedManager renderDetail
