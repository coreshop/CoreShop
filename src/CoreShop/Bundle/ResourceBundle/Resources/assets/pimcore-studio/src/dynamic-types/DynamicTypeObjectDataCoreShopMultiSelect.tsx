import React from 'react'
import {
  DynamicTypeObjectDataAbstractMultiSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'
import { EntityMultiSelect } from '../components/EntitySelect'
import type { SelectOption } from '../utils/createOptionsLoader'

interface MultiSelectWrapperProps {
  value?: string[]
  onChange?: (value: string[]) => void
  disabled?: boolean
  style?: React.CSSProperties
  loadOptions: () => Promise<SelectOption[]>
  getCachedOptions?: () => SelectOption[] | null
}

const MultiSelectWrapper: React.FC<MultiSelectWrapperProps> = ({
  value,
  onChange,
  loadOptions,
  getCachedOptions,
  ...rest
}) => {
  const numericValue = value?.map((v) => Number(v)) ?? []

  const handleChange = (values: number[]) => {
    onChange?.(values?.map((v) => String(v)) ?? [])
  }

  return (
    <EntityMultiSelect
      {...rest}
      value={numericValue}
      onChange={handleChange}
      loadOptions={loadOptions}
      getCachedOptions={getCachedOptions}
    />
  )
}

export abstract class DynamicTypeObjectDataCoreShopMultiSelect extends DynamicTypeObjectDataAbstractMultiSelect {
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  abstract loadOptions(): Promise<SelectOption[]>
  getCachedOptions?(): SelectOption[] | null

  getObjectDataComponent(props: any): React.ReactElement {
    const { noteditable, defaultFieldWidth } = props

    return (
      <MultiSelectWrapper
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
        loadOptions={() => this.loadOptions()}
        getCachedOptions={this.getCachedOptions ? () => this.getCachedOptions!() : undefined}
      />
    )
  }
}
