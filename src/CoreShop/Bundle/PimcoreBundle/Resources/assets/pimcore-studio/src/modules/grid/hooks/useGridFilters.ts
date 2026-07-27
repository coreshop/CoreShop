/**
 * CoreShop PimcoreBundle useGridFilters Hook
 *
 * Hook for loading and managing grid filters for a specific list type.
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
import { fetchGridFilters, clearGridCache, type GridFilter } from '../api'

interface UseGridFiltersResult {
  filters: GridFilter[]
  loading: boolean
  error: Error | null
  reload: () => Promise<void>
}

/**
 * Hook to load grid filters for a specific list type
 * Filters are loaded fresh on each mount (cache is cleared)
 *
 * @param listType - The type of list (e.g., 'coreshop_order', 'coreshop_cart')
 */
export const useGridFilters = (listType: string): UseGridFiltersResult => {
  const [filters, setFilters] = React.useState<GridFilter[]>([])
  const [loading, setLoading] = React.useState(true)
  const [error, setError] = React.useState<Error | null>(null)

  const loadFilters = React.useCallback(async (clearCacheFirst = false) => {
    setLoading(true)
    setError(null)

    try {
      if (clearCacheFirst) {
        clearGridCache(listType)
      }
      const data = await fetchGridFilters(listType)
      setFilters(data)
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Failed to load filters'))
    } finally {
      setLoading(false)
    }
  }, [listType])

  React.useEffect(() => {
    // Clear cache and load fresh on mount
    void loadFilters(true)
  }, [loadFilters])

  return {
    filters,
    loading,
    error,
    reload: () => loadFilters(true)
  }
}
