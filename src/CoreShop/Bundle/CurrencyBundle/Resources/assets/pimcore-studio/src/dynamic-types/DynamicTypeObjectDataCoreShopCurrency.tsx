/**
 * CoreShop CurrencyBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Select } from 'antd'
import {
  DynamicTypeObjectDataAbstractSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'
import { currencyApi } from '../modules/currencies/api'

export type Option = { value: number, label: string }

// Module-level cache
let cachedOptions: Option[] | null = null
let loadPromise: Promise<Option[]> | null = null

export const loadCurrencies = async (): Promise<Option[]> => {
  if (cachedOptions) return cachedOptions
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const rows = await currencyApi.list()
      const list = Array.isArray(rows) ? rows : []
      cachedOptions = list
        .map((r: any) => ({ value: r.id, label: r.name ?? r.isoCode ?? String(r.id) }))
        .filter((o: any) => o.value != null && o.label)
      return cachedOptions
    } catch (err) {
      console.error('Failed to load currencies:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearCurrencyCache = () => {
  cachedOptions = null
  loadPromise = null
}

const CurrencySelectInner: React.FC<{
  value?: number
  onChange?: (value: number) => void
  disabled?: boolean
  style?: React.CSSProperties
}> = ({ value, onChange, disabled, style }) => {
  const [options, setOptions] = React.useState<Option[]>([])
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    void (async () => {
      try {
        const opts = await loadCurrencies()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <Select
      value={value}
      onChange={onChange}
      options={options}
      loading={loading}
      disabled={disabled}
      style={style}
      showSearch
      optionFilterProp="label"
      allowClear
    />
  )
}

export class DynamicTypeObjectDataCoreShopCurrency extends DynamicTypeObjectDataAbstractSelect {
  readonly id = 'coreShopCurrency'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    return (
      <CurrencySelectInner
        value={rest.value}
        onChange={rest.onChange}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
      />
    )
  }
}
