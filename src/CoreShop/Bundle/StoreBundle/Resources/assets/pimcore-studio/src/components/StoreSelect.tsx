/**
 * CoreShop StoreBundle Studio Plugin
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
import { storeApi } from '../modules/stores/api'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'

// Module-level cache to avoid multiple API calls
let cachedOptions: Array<{ value: number, label: string }> | null = null
let loadPromise: Promise<Array<{ value: number, label: string }>> | null = null

const loadStores = async (): Promise<Array<{ value: number, label: string }>> => {
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
      const stores = await storeApi.list()
      cachedOptions = stores.map(store => ({
        value: store.id!,
        label: store.name
      }))
      return cachedOptions
    } catch (err) {
      console.error('Failed to load stores:', err)
      throw err
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed
export const clearStoreCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const StoreSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadStores()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  return (
    <DroppableEntity
      accept="coreshop:store"
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
      />
    </DroppableEntity>
  )
}
