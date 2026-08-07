import React from 'react'
import type { SelectProps } from 'antd'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { EntitySelect } from '@coreshop/resource/src/components/EntitySelect'
import { carrierApi } from '../modules/carriers/api'

const { load: loadCarriers, getCache: getCarrierCache, clearCache: clearCarrierCache } = createOptionsLoader(async () => {
  const carriers = await carrierApi.list()
  return carriers.map(carrier => ({
    value: carrier.id!,
    label: carrier.identifier ?? `#${carrier.id}`
  }))
})

export { loadCarriers, getCarrierCache, clearCarrierCache }

export const CarrierSelect: React.FC<SelectProps> = (props) => {
  return (
    <EntitySelect
      {...props}
      loadOptions={loadCarriers}
      getCachedOptions={getCarrierCache}
      placeholder={props.placeholder ?? 'Select a carrier'}
    />
  )
}
