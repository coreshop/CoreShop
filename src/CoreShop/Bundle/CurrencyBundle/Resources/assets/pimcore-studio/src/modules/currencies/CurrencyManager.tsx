import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import type { CurrencyDetail } from './api'
import { currencyApi } from './api'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { CurrencyForm } from './CurrencyForm'

export const CurrencyManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager<CurrencyDetail>
      api={ currencyApi }
      dragType='coreshop:currency'
      leftRootTitle='Currencies'
      getTitle={ (li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}` }
      buildSavePayload={ (data) => ({ id: data.id, name: data.name, isoCode: data.isoCode, numericIsoCode: data.numericIsoCode, symbol: data.symbol }) }
      onAdd={ async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add Currency',
          label: 'Name',
          rule: { required: true, message: 'Name is required' },
          onOk: async (nameValue: string) => {
            const res = await currencyApi.add({ name: nameValue })
            resolve(res.data.id)
          }
        })
      }) }
      renderDetail={ (data, setData) => (
        <CurrencyForm data={ data } onChange={ setData } />
      ) }
    />
  )
}
