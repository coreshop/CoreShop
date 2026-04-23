/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'

export interface DynamicDropdownOption {
  value: number
  label: string
  published: boolean
}

export interface DynamicDropdownFieldConfig {
  folderName?: string
  className?: string
  methodName?: string
  recursive?: boolean | string
  sortBy?: string
  onlyPublished?: boolean
}

interface ApiResponse {
  success: boolean
  message?: string
  options: Array<{
    value: number
    key: string
    published: boolean
  }>
}

// Cache keyed by field config params to avoid duplicate calls for same config
const optionsCache = new Map<string, DynamicDropdownOption[]>()
const loadPromises = new Map<string, Promise<DynamicDropdownOption[]>>()

function buildCacheKey(config: DynamicDropdownFieldConfig): string {
  return JSON.stringify({
    f: config.folderName,
    c: config.className,
    m: config.methodName,
    r: config.recursive,
    s: config.sortBy
  })
}

async function fetchOptions(config: DynamicDropdownFieldConfig): Promise<DynamicDropdownOption[]> {
  const cacheKey = buildCacheKey(config)

  const cached = optionsCache.get(cacheKey)
  if (cached) {
    return cached
  }

  const existing = loadPromises.get(cacheKey)
  if (existing) {
    return existing
  }

  const promise = (async () => {
    try {
      const params = new URLSearchParams()
      if (config.folderName) params.set('folderName', config.folderName)
      if (config.className) params.set('className', config.className)
      if (config.methodName) params.set('methodName', config.methodName)
      if (config.recursive) params.set('recursive', String(config.recursive))
      if (config.sortBy) params.set('sortBy', config.sortBy)
      params.set('current_language', document.documentElement.lang || 'en')

      const response = await fetch(`/pimcore-studio/api/coreshop/dynamic-dropdown/options?${params.toString()}`)
      const data: ApiResponse = await response.json()

      if (!data.success) {
        console.error('Failed to load dynamic dropdown options:', data.message)
        return []
      }

      const options: DynamicDropdownOption[] = data.options.map(opt => ({
        value: opt.value,
        label: opt.key,
        published: opt.published
      }))

      optionsCache.set(cacheKey, options)
      return options
    } catch (err) {
      console.error('Failed to load dynamic dropdown options:', err)
      return []
    } finally {
      loadPromises.delete(cacheKey)
    }
  })()

  loadPromises.set(cacheKey, promise)
  return promise
}

export function clearDynamicDropdownCache(): void {
  optionsCache.clear()
  loadPromises.clear()
}

export function useDynamicDropdownOptions(config: DynamicDropdownFieldConfig): {
  options: DynamicDropdownOption[]
  loading: boolean
} {
  const cacheKey = buildCacheKey(config)
  const [options, setOptions] = React.useState<DynamicDropdownOption[]>(
    optionsCache.get(cacheKey) ?? []
  )
  const [loading, setLoading] = React.useState(!optionsCache.has(cacheKey))

  React.useEffect(() => {
    if (!config.folderName || !config.className || !config.methodName) {
      setLoading(false)
      return
    }

    void (async () => {
      if (!optionsCache.has(cacheKey)) {
        setLoading(true)
      }
      try {
        const opts = await fetchOptions(config)
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [cacheKey])

  return { options, loading }
}
