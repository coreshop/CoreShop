import React from 'react'
import type { SelectProps } from 'antd'
import { EntityMultiSelect } from '@coreshop/resource/src/components/EntitySelect'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { loadStores, getStoreCache, clearStoreCache } from './StoreSelect'

export { clearStoreCache }

export const StoreMultiSelectField: React.FC<SelectProps<number[]>> = (props) => {
  return (
    <DroppableEntity
      accept='coreshop:store'
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (props.onChange && info?.data?.id) {
          const currentValue = props.value || []
          const newValue = Array.isArray(currentValue)
            ? [...currentValue, info.data.id]
            : [info.data.id]
          const event = { target: { value: newValue } } as any
          props.onChange(newValue, event)
        }
      }}
    >
      <EntityMultiSelect
        {...props}
        loadOptions={loadStores}
        getCachedOptions={getStoreCache}
      />
    </DroppableEntity>
  )
}
