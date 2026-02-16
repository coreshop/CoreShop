import React from 'react'
import type { SelectProps } from 'antd'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { EntitySelect } from '@coreshop/resource/src/components/EntitySelect'
import { storeApi } from '../modules/stores/api'

const { load: loadStores, getCache: getStoreCache, clearCache: clearStoreCache } = createOptionsLoader(async () => {
  const stores = await storeApi.list()
  return stores.map(store => ({
    value: store.id!,
    label: store.name
  }))
})

export { loadStores, getStoreCache, clearStoreCache }

export const StoreSelect: React.FC<SelectProps> = (props) => {
  return (
    <DroppableEntity
      accept="coreshop:store"
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
        loadOptions={loadStores}
        getCachedOptions={getStoreCache}
      />
    </DroppableEntity>
  )
}
