import React from 'react'
import type { SelectProps } from 'antd'
import { DroppableEntity } from '../entities/components/dnd/DroppableEntity'
import { EntitySelect, EntityMultiSelect } from './EntitySelect'
import type { SelectOption } from '../utils/createOptionsLoader'

export interface EntityChoiceWidgetProps extends Omit<SelectProps, 'options' | 'loading'> {
  loadOptions: () => Promise<SelectOption[]>
  getCachedOptions?: () => SelectOption[] | null
  droppableAccept?: string
}

export const EntityChoiceWidget: React.FC<EntityChoiceWidgetProps> = ({
  loadOptions,
  getCachedOptions,
  droppableAccept,
  ...selectProps
}) => {
  const isMultiple = selectProps.mode === 'multiple'

  const select = isMultiple
    ? <EntityMultiSelect {...selectProps as any} loadOptions={loadOptions} getCachedOptions={getCachedOptions} />
    : <EntitySelect {...selectProps} loadOptions={loadOptions} getCachedOptions={getCachedOptions} />

  if (!droppableAccept) {
    return select
  }

  return (
    <DroppableEntity
      accept={droppableAccept}
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (!selectProps.onChange || !info?.data?.id) return
        if (isMultiple) {
          const currentValue = (selectProps.value as number[]) || []
          const newValue = Array.isArray(currentValue)
            ? [...currentValue, info.data.id]
            : [info.data.id]
          ;(selectProps.onChange as any)(newValue, { target: { value: newValue } })
        } else {
          ;(selectProps.onChange as any)(info.data.id, { target: { value: info.data.id } })
        }
      }}
    >
      {select}
    </DroppableEntity>
  )
}
