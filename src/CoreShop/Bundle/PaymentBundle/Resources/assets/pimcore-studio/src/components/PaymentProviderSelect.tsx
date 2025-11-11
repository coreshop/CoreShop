/**
 * CoreShop PaymentBundle - Payment Provider Select
 *
 * Select component for choosing payment providers with module-level caching.
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
import { paymentProviderApi } from '../modules/payment-providers/api'

// Module-level cache to avoid multiple API calls
let cachedOptions: Array<{ value: number; label: string }> | null = null
let loadPromise: Promise<Array<{ value: number; label: string }>> | null = null

const loadPaymentProviders = async (): Promise<Array<{ value: number; label: string }>> => {
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
      const providers = await paymentProviderApi.list()
      cachedOptions = providers.map(provider => ({
        value: provider.id!,
        label: provider.identifier ?? `#${provider.id}`
      }))
      return cachedOptions
    } catch (err) {
      console.error('Failed to load payment providers:', err)
      throw err
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

// Export function to clear cache if needed (e.g., after creating new provider)
export const clearPaymentProviderCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const PaymentProviderSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number; label: string }>>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadPaymentProviders()
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
      placeholder={props.placeholder ?? 'Select a payment provider'}
      showSearch
      filterOption={(input, option) =>
        (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}
