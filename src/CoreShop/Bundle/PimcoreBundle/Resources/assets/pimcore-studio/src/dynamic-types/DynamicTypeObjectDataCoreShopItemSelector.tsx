/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Transfer } from 'antd'
import {
  DynamicTypeObjectDataAbstractMultiSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'
import {
  useDynamicDropdownOptions,
  type DynamicDropdownFieldConfig
} from './useDynamicDropdownOptions'

interface TransferItem {
  key: string
  title: string
}

const ItemSelectorComponent: React.FC<{
  value?: number[]
  onChange?: (value: number[]) => void
  disabled?: boolean
  style?: React.CSSProperties
  fieldConfig: DynamicDropdownFieldConfig
}> = ({ value, onChange, disabled, style, fieldConfig }) => {
  const { options, loading } = useDynamicDropdownOptions(fieldConfig)

  const dataSource: TransferItem[] = React.useMemo(
    () => options.map(opt => ({
      key: String(opt.value),
      title: opt.label
    })),
    [options]
  )

  const targetKeys = React.useMemo(
    () => (value ?? []).map(String),
    [value]
  )

  const handleChange = React.useCallback(
    (nextTargetKeys: string[]) => {
      onChange?.(nextTargetKeys.map(Number))
    },
    [onChange]
  )

  return (
    <Transfer
      dataSource={dataSource}
      targetKeys={targetKeys}
      onChange={handleChange}
      render={item => item.title}
      disabled={disabled || loading}
      showSearch
      filterOption={(inputValue, item) =>
        item.title.toLowerCase().includes(inputValue.toLowerCase())
      }
      titles={['Available', 'Selected']}
      listStyle={{ width: 250, height: 300, ...style }}
    />
  )
}

export class DynamicTypeObjectDataCoreShopItemSelector extends DynamicTypeObjectDataAbstractMultiSelect {
  readonly id = 'coreShopItemSelector'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  gridCellEditMode = 'edit-modal' as const
  gridCellEditModalSettings = {
    modalSize: 'L' as const,
    formLayout: 'vertical' as const
  }

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
      <ItemSelectorComponent
        value={rest.value}
        onChange={rest.onChange}
        disabled={noteditable === true}
        fieldConfig={fieldConfig}
      />
    )
  }
}
