import React from 'react'
import type { SelectProps } from 'antd'
import { EntityMultiSelect } from '@coreshop/resource/src/components/EntitySelect'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { loadTaxRates, getTaxRateCache, clearTaxRateCache } from './TaxRateSelect'

export { clearTaxRateCache }

export const TaxRateMultiSelectField: React.FC<SelectProps<number[]>> = (props) => {
  return (
    <DroppableEntity
      accept='coreshop:tax_rate'
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
        loadOptions={loadTaxRates}
        getCachedOptions={getTaxRateCache}
      />
    </DroppableEntity>
  )
}
