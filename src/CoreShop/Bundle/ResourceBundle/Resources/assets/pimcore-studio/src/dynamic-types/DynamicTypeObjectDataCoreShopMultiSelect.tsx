import React from 'react'
import {
  DynamicTypeObjectDataAbstractMultiSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'
import { EntityMultiSelect } from '../components/EntitySelect'
import type { SelectOption } from '../utils/createOptionsLoader'

export abstract class DynamicTypeObjectDataCoreShopMultiSelect extends DynamicTypeObjectDataAbstractMultiSelect {
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  abstract loadOptions(): Promise<SelectOption[]>
  getCachedOptions?(): SelectOption[] | null

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    return (
      <EntityMultiSelect
        value={rest.value?.map((v: any) => typeof v === 'string' ? Number(v) : v)}
        onChange={rest.onChange}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
        loadOptions={() => this.loadOptions()}
        getCachedOptions={this.getCachedOptions ? () => this.getCachedOptions!() : undefined}
      />
    )
  }
}
