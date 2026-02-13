/**
 * CoreShop Schema Adapter - Autocomplete Widget
 *
 * Server-side search select for large entity collections.
 * Used by AutocompleteType to avoid loading all records into memory.
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

const DEFAULT_SEARCH_URL = '/pimcore-studio/api/coreshop/object-search'
const DEBOUNCE_MS = 300

interface SearchResult {
  id: number
  label: string
}

interface AutocompleteWidgetProps extends Omit<SelectProps, 'onChange'> {
  value?: number | number[]
  onChange?: (value: number | number[] | undefined) => void
  autocompleteClass?: string
  url?: string
  multiple?: boolean
  minChars?: number
}

export const AutocompleteWidget: React.FC<AutocompleteWidgetProps> = ({
  value,
  onChange,
  autocompleteClass,
  url,
  multiple = false,
  minChars = 1,
  ...restProps
}) => {
  const [options, setOptions] = React.useState<Array<{ value: number; label: string }>>([])
  const [loading, setLoading] = React.useState(false)
  const [initialResolved, setInitialResolved] = React.useState(false)
  const debounceRef = React.useRef<ReturnType<typeof setTimeout> | null>(null)
  const abortRef = React.useRef<AbortController | null>(null)

  const searchUrl = url ?? DEFAULT_SEARCH_URL

  const fetchResults = React.useCallback(async (params: Record<string, string>, signal?: AbortSignal) => {
    const query = new URLSearchParams(params)

    if (autocompleteClass && !params.class) {
      query.set('class', autocompleteClass)
    }

    const response = await fetch(`${searchUrl}?${query.toString()}`, { signal })

    if (!response.ok) {
      throw new Error(`Search failed: ${response.status}`)
    }

    return (await response.json()) as SearchResult[]
  }, [searchUrl, autocompleteClass])

  // Resolve initial IDs to labels
  React.useEffect(() => {
    if (initialResolved) return

    const ids = Array.isArray(value) ? value : (value != null ? [value] : [])

    if (ids.length === 0) {
      setInitialResolved(true)
      return
    }

    let cancelled = false

    const resolve = async () => {
      try {
        const results = await fetchResults({ ids: ids.join(',') })

        if (!cancelled) {
          setOptions(results.map(r => ({ value: r.id, label: r.label })))
          setInitialResolved(true)
        }
      } catch {
        if (!cancelled) {
          setInitialResolved(true)
        }
      }
    }

    void resolve()

    return () => { cancelled = true }
  }, [value, initialResolved, fetchResults])

  const handleSearch = React.useCallback((searchText: string) => {
    if (debounceRef.current) {
      clearTimeout(debounceRef.current)
    }

    if (abortRef.current) {
      abortRef.current.abort()
    }

    if (searchText.length < minChars) {
      return
    }

    debounceRef.current = setTimeout(() => {
      const controller = new AbortController()
      abortRef.current = controller

      setLoading(true)

      fetchResults({ query: searchText }, controller.signal)
        .then(results => {
          // Merge with current selected options to keep labels
          const currentValues = Array.isArray(value) ? value : (value != null ? [value] : [])
          const existingOptions = options.filter(o => currentValues.includes(o.value))
          const newOptions = results.map(r => ({ value: r.id, label: r.label }))

          // Deduplicate
          const merged = [...existingOptions]
          for (const opt of newOptions) {
            if (!merged.some(m => m.value === opt.value)) {
              merged.push(opt)
            }
          }

          setOptions(merged)
          setLoading(false)
        })
        .catch(err => {
          if (err.name !== 'AbortError') {
            setLoading(false)
          }
        })
    }, DEBOUNCE_MS)
  }, [fetchResults, minChars, value, options])

  const handleChange = React.useCallback((newValue: number | number[] | undefined) => {
    onChange?.(newValue)
  }, [onChange])

  // Cleanup on unmount
  React.useEffect(() => {
    return () => {
      if (debounceRef.current) {
        clearTimeout(debounceRef.current)
      }
      if (abortRef.current) {
        abortRef.current.abort()
      }
    }
  }, [])

  return (
    <Select
      {...restProps}
      value={value}
      onChange={handleChange}
      loading={loading}
      options={options}
      mode={multiple ? 'multiple' : undefined}
      showSearch
      filterOption={false}
      onSearch={handleSearch}
      allowClear
      notFoundContent={loading ? 'Searching...' : 'Type to search'}
      placeholder={restProps.placeholder ?? 'Type to search...'}
    />
  )
}
