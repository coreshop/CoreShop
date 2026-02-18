import React from 'react'
import type { SelectProps } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { EntitySelect } from '@coreshop/resource/src/components/EntitySelect'
import { taxRateApi } from '../modules/tax-rates/api'

const { load: loadTaxRates, getCache: getTaxRateCache, clearCache: clearTaxRateCache } = createOptionsLoader(async () => {
  const rows = await taxRateApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export { loadTaxRates, getTaxRateCache, clearTaxRateCache }

export const TaxRateSelect: React.FC<SelectProps> = (props) => {
  return (
    <DroppableEntity
      accept="coreshop:tax_rate"
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (props.onChange && info?.data?.id) {
          const event = { target: { value: info.data.id } } as any
          props.onChange(info.data.id, event)
        }
      }}
    >
      <EntitySelect
        {...props}
        loadOptions={loadTaxRates}
        getCachedOptions={getTaxRateCache}
      />
    </DroppableEntity>
  )
}
