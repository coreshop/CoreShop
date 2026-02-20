/**
 * CoreShop IndexBundle Quantity Unit Select
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
import { Select, type SelectProps } from 'antd'

interface QuantityValueUnit {
  id: string | null
  abbreviation: string | null
  group: string | null
  longName: string | null
}

// Module-level cache to avoid multiple API calls
let cachedOptions: Array<{ value: string, label: string }> | null = null
let loadPromise: Promise<Array<{ value: string, label: string }>> | null = null

const loadQuantityUnits = async (): Promise<Array<{ value: string, label: string }>> => {
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
      const url = '/pimcore-studio/api/unit/quantity-value/unit-list'
      const response = await fetch(url, {
        credentials: 'same-origin'
      })

      if (!response.ok) {
        throw new Error(`Failed to load quantity units: ${response.statusText}`)
      }

      const data: { items: QuantityValueUnit[] } = await response.json()

      // Add "Empty" option at the beginning (id: "0")
      cachedOptions = [
        { value: '0', label: 'Empty' },
        ...data.items
          .filter(unit => unit.id !== null)
          .map(unit => ({
            value: unit.id!,
            label: unit.id! // Use id as label since abbreviation is empty
          }))
      ]

      return cachedOptions
    } catch (err) {
      console.error('Failed to load quantity units:', err)
      // Return empty option on error
      cachedOptions = [{ value: '0', label: 'Empty' }]
      return cachedOptions
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed
export const clearQuantityUnitCache = () => {
  cachedOptions = null
  loadPromise = null
}

interface QuantityUnitSelectProps extends Omit<SelectProps, 'options' | 'loading'> {
  value?: string | number
  onChange?: (value: string | number) => void
}

/**
 * QuantityUnitSelect - Select component for Pimcore Quantity Units
 *
 * This component loads and displays available quantity units from Pimcore.
 * Used in filter conditions to specify the unit for numeric values.
 */
export const QuantityUnitSelect: React.FC<QuantityUnitSelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: string, label: string }>>(
    cachedOptions || [{ value: '0', label: 'Empty' }]
  )
  const [loading, setLoading] = React.useState(!cachedOptions)

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadQuantityUnits()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <Select
      {...props}
      loading={loading}
      options={options}
      placeholder={props.placeholder ?? 'Select quantity unit'}
      showSearch
      filterOption={(input, option) =>
        String(option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}
