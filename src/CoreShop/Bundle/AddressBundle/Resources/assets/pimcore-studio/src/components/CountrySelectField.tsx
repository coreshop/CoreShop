/**
 * CoreShop AddressBundle - Country Select Field (without Form.Item)
 *
 * Pure Select component for use in FormBuilder.
 * For standalone use with Form.Item, use CountrySelect instead.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Select } from 'antd'
import type { SelectProps } from 'antd'
import { useTranslation } from 'react-i18next'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { countryApi } from '../modules/countries/api'

type Option = { value: number, label: string }

// Module-level cache to avoid multiple API calls
let cachedOptions: Option[] | null = null
let loadPromise: Promise<Option[]> | null = null

const loadCountries = async (): Promise<Option[]> => {
  // Return cached data if available
  if (cachedOptions) {
    return cachedOptions
  }

  // If already loading, return the existing promise
  if (loadPromise) {
    return loadPromise
  }

  // Start new load
  loadPromise = (async () => {
    try {
      const rows = await countryApi.list()
      const list = Array.isArray(rows) ? rows : []
      cachedOptions = list
        .map((r: any) => ({ value: r.id, label: r.name ?? r.isoCode ?? String(r.id) }))
        .filter((o: any) => o.value != null && o.label)
      return cachedOptions
    } catch (err) {
      console.error('Failed to load countries:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed
export const clearCountryCache = () => {
  cachedOptions = null
  loadPromise = null
}

/**
 * CountrySelectField - Pure Select without Form.Item
 *
 * For use in FormBuilder where DynamicForm provides the Form.Item wrapper.
 */
export const CountrySelectField: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Option[]>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)
  const { t } = useTranslation()

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadCountries()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <DroppableEntity
      accept='coreshop:country'
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (props.onChange && info?.data?.id) {
          const event = { target: { value: info.data.id } } as any
          props.onChange(info.data.id, event)
        }
      }}
    >
      <Select
        {...props}
        loading={loading}
        options={options}
        placeholder={props.placeholder ?? t('coreshop.ui.select', { defaultValue: 'Select' })}
        showSearch
        optionFilterProp="label"
      />
    </DroppableEntity>
  )
}
