import React from 'react'
import {
  DynamicTypeObjectDataAbstractSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'
import { EntitySelect } from '../components/EntitySelect'
import type { SelectOption } from '../utils/createOptionsLoader'

export abstract class DynamicTypeObjectDataCoreShopSelect extends DynamicTypeObjectDataAbstractSelect {
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  abstract loadOptions(): Promise<SelectOption[]>
  getCachedOptions?(): SelectOption[] | null

  getObjectDataComponent(props: any): React.ReactElement {
    const { noteditable, defaultFieldWidth } = props

    return (
      <EntitySelect
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
        loadOptions={() => this.loadOptions()}
        getCachedOptions={this.getCachedOptions ? () => this.getCachedOptions!() : undefined}
        allowClear
      />
    )
  }
}
