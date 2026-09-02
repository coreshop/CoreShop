import React from 'react'
import { Select } from 'antd'
import type { SelectProps } from 'antd'
import { useTranslation } from 'react-i18next'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'

type Option = { value: number, label: string }

// Category selection typically uses Pimcore's DataObject picker
// This is a simplified version for FormBuilder use
export const CategoryMultiSelectField: React.FC<SelectProps<number[]>> = (props) => {
  const { t } = useTranslation()

  return (
    <DroppableEntity
      accept='coreshop:category'
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
      <Select
        {...props}
        mode="multiple"
        placeholder={props.placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select Categories' })}
        showSearch
        optionFilterProp="label"
      />
    </DroppableEntity>
  )
}
