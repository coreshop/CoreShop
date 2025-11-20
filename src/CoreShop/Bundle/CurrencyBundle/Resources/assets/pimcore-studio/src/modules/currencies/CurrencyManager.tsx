import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import type { CurrencyDetail } from './api'
import { currencyApi } from './api'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { CurrencyForm } from './CurrencyForm'
import { useTranslation } from 'react-i18next'

export const CurrencyManager: React.FC = () => {
  const modal = useFormModal()
  const { t } = useTranslation()

  return (
    <EntityTabbedManager<CurrencyDetail>
      api={ currencyApi }
      dragType='coreshop:currency'
      leftRootTitle={t('coreshop_currencies', { defaultValue: 'Currencies' })}
      getTitle={ (li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}` }
      buildSavePayload={ (data) => data }
      onAdd={ async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_currency_add', { defaultValue: 'Add Currency' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (nameValue: string) => {
            const res = await currencyApi.add({ name: nameValue })
            resolve(res.data.id)
          }
        })
      }) }
      renderDetail={ (data, setData, ctx) => (
        <CurrencyForm
          data={data}
          onChange={setData}
          currentLocale={ctx?.currentLocale ?? 'en'}
          locales={ctx?.locales}
        />
      ) }
    />
  )
}
