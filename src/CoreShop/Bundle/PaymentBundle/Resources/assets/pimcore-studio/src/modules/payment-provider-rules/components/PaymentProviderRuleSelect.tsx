/**
 * CoreShop PaymentBundle Studio Plugin
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
import { paymentProviderRuleApi } from '../api'

// Module-level cache to avoid multiple API calls
let cachedOptions: Array<{ value: number; label: string }> | null = null
let loadPromise: Promise<Array<{ value: number; label: string }>> | null = null

const loadPaymentProviderRules = async (): Promise<Array<{ value: number; label: string }>> => {
  if (cachedOptions) {
    return cachedOptions
  }

  if (loadPromise) {
    return loadPromise
  }

  loadPromise = (async () => {
    try {
      const items = await paymentProviderRuleApi.list()
      const result = items.map((rule: any) => ({
        value: rule.id!,
        label: rule.name ?? `#${rule.id}`
      }))
      cachedOptions = result
      return result
    } catch (err) {
      console.error('Failed to load payment provider rules:', err)
      throw err
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearPaymentProviderRuleCache = () => {
  cachedOptions = null
  loadPromise = null
}

export const PaymentProviderRuleSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number; label: string }>>(cachedOptions || [])
  const [loading, setLoading] = React.useState(!cachedOptions)

  React.useEffect(() => {
    void (async () => {
      if (!cachedOptions) {
        setLoading(true)
      }
      try {
        const opts = await loadPaymentProviderRules()
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
      placeholder={props.placeholder ?? 'Select a payment provider rule'}
      showSearch
      filterOption={(input, option) =>
        String(option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
    />
  )
}
