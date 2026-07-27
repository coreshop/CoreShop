/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Select } from 'antd'
import {
  DynamicTypeObjectDataAbstractMultiSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'
import {
  useDynamicDropdownOptions,
  type DynamicDropdownFieldConfig
} from './useDynamicDropdownOptions'

const DynamicDropdownMultiSelect: React.FC<{
  value?: number[]
  onChange?: (value: number[]) => void
  disabled?: boolean
  style?: React.CSSProperties
  fieldConfig: DynamicDropdownFieldConfig
}> = ({ value, onChange, disabled, style, fieldConfig }) => {
  const { options, loading } = useDynamicDropdownOptions(fieldConfig)

  return (
    <Select
      mode="multiple"
      value={value}
      onChange={onChange}
      loading={loading}
      disabled={disabled}
      style={style}
      showSearch
      optionFilterProp="label"
      options={options}
    />
  )
}

export class DynamicTypeObjectDataCoreShopDynamicDropdownMultiple extends DynamicTypeObjectDataAbstractMultiSelect {
  readonly id = 'coreShopDynamicDropdownMultiple'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    const fieldConfig: DynamicDropdownFieldConfig = {
      folderName: props.folderName,
      className: props.className,
      methodName: props.methodName,
      recursive: props.recursive,
      sortBy: props.sortBy
    }

    return (
      <DynamicDropdownMultiSelect
        value={rest.value}
        onChange={rest.onChange}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
        fieldConfig={fieldConfig}
      />
    )
  }
}
