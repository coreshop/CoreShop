/**
 * CoreShop StoreBundle Studio Plugin
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
import { loadStores, clearStoreCache } from '../components/StoreMultiSelect'

// Re-export for external use
export { clearStoreCache }

const StoreMultiSelectInner: React.FC<{
  value?: number[]
  onChange?: (value: number[]) => void
  disabled?: boolean
  style?: React.CSSProperties
}> = ({ value, onChange, disabled, style }) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>([])
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    void (async () => {
      try {
        const opts = await loadStores()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <Select
      mode="multiple"
      value={value}
      onChange={onChange}
      options={options}
      loading={loading}
      disabled={disabled}
      style={style}
      showSearch
      optionFilterProp="label"
      maxTagCount="responsive"
    />
  )
}

export class DynamicTypeObjectDataCoreShopStoreMultiselect extends DynamicTypeObjectDataAbstractMultiSelect {
  readonly id = 'coreShopStoreMultiselect'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    return (
      <StoreMultiSelectInner
        value={rest.value}
        onChange={rest.onChange}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
      />
    )
  }
}
