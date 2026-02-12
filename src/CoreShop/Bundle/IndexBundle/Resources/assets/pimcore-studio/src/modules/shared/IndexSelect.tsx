/**
 * CoreShop IndexBundle Index Select Component
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
import { indexApi } from '../indexes/api'

// Module-level cache to avoid multiple API calls
let cachedOptions: Array<{ value: number, label: string }> | null = null
let loadPromise: Promise<Array<{ value: number, label: string }>> | null = null

const loadIndexes = async (): Promise<Array<{ value: number, label: string }>> => {
  // Return cached data if available
  if (cachedOptions) {
    return cachedOptions
  }

  // If already loading, return the existing promise (prevents race conditions)
  if (loadPromise) {
    return loadPromise
  }

  // Start new load
  loadPromise = (async () => {
    try {
      const indexes = await indexApi.list()
      cachedOptions = indexes.map(index => ({
        value: index.id!,
        label: index.name ?? `#${index.id}`
      }))
      return cachedOptions
    } catch (err) {
      throw err
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed (e.g., after creating new item)
export const clearIndexCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const IndexSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadIndexes()
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
      placeholder={props.placeholder ?? 'Select an index'}
      showSearch
      filterOption={(input, option) =>
        String(option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}
