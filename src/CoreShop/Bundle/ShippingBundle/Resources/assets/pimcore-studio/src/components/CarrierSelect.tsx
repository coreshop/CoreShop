/**
 * CoreShop ShippingBundle - Carrier Select Component
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
import { carrierApi } from '../modules/carriers/api'

// Module-level cache to avoid multiple API calls
let cachedOptions: Array<{ value: number, label: string }> | null = null
let loadPromise: Promise<Array<{ value: number, label: string }>> | null = null

const loadCarriers = async (): Promise<Array<{ value: number, label: string }>> => {
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
      const carriers = await carrierApi.list()
      cachedOptions = carriers.map(carrier => ({
        value: carrier.id!,
        label: carrier.identifier ?? `#${carrier.id}`
      }))
      return cachedOptions
    } catch (err) {
      console.error('Failed to load carriers:', err)
      throw err
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed
export const clearCarrierCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const CarrierSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadCarriers()
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
      placeholder={props.placeholder ?? 'Select a carrier'}
      showSearch
      filterOption={(input, option) =>
        (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}
