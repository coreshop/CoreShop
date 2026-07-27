/**
 * CoreShop PimcoreBundle Grid API
 *
 * API functions for fetching grid filters and actions with module-level caching.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export interface GridFilter {
  id: string
  name: string
}

export interface GridAction {
  id: string
  name: string
}

export interface ActionResult {
  success: boolean
  message: string
}

// Module-level caches
const filtersCache = new Map<string, GridFilter[]>()
const actionsCache = new Map<string, GridAction[]>()

// Promises for in-flight requests to prevent duplicate calls
let filtersLoadPromise = new Map<string, Promise<GridFilter[]>>()
let actionsLoadPromise = new Map<string, Promise<GridAction[]>>()

/**
 * Fetch grid filters for a specific list type (Studio v2 compatible filters)
 * Results are cached to prevent multiple API calls
 *
 * Uses the studio-filters endpoint which returns StudioGridFilterInterface implementations
 * that work with OpenSearch queries.
 */
export const fetchGridFilters = async (listType: string): Promise<GridFilter[]> => {
  // Return cached data if available
  if (filtersCache.has(listType)) {
    return filtersCache.get(listType)!
  }

  // If already loading, return the existing promise
  if (filtersLoadPromise.has(listType)) {
    return filtersLoadPromise.get(listType)!
  }

  // Start new load
  const promise = (async (): Promise<GridFilter[]> => {
    try {
      // Use studio-filters endpoint for Studio v2 compatible filters
      const response = await fetch(`/pimcore-studio/api/coreshop/grid/studio-filters/${encodeURIComponent(listType)}?studio=1`)

      if (!response.ok) {
        throw new Error(`Failed to fetch grid filters: ${response.statusText}`)
      }

      const data: GridFilter[] = await response.json()
      filtersCache.set(listType, data)
      return data
    } catch (err) {
      console.error('[CoreShop Grid] Failed to load filters:', err)
      throw err
    } finally {
      filtersLoadPromise.delete(listType)
    }
  })()

  filtersLoadPromise.set(listType, promise)
  return promise
}

/**
 * Fetch grid actions for a specific list type
 * Results are cached to prevent multiple API calls
 */
export const fetchGridActions = async (listType: string): Promise<GridAction[]> => {
  // Return cached data if available
  if (actionsCache.has(listType)) {
    return actionsCache.get(listType)!
  }

  // If already loading, return the existing promise
  if (actionsLoadPromise.has(listType)) {
    return actionsLoadPromise.get(listType)!
  }

  // Start new load
  const promise = (async (): Promise<GridAction[]> => {
    try {
      const response = await fetch(`/pimcore-studio/api/coreshop/grid/actions/${encodeURIComponent(listType)}`)

      if (!response.ok) {
        throw new Error(`Failed to fetch grid actions: ${response.statusText}`)
      }

      const data: GridAction[] = await response.json()
      actionsCache.set(listType, data)
      return data
    } catch (err) {
      console.error('[CoreShop Grid] Failed to load actions:', err)
      throw err
    } finally {
      actionsLoadPromise.delete(listType)
    }
  })()

  actionsLoadPromise.set(listType, promise)
  return promise
}

/**
 * Apply a grid action to selected IDs
 */
export const applyGridAction = async (actionId: string, ids: number[]): Promise<ActionResult> => {
  try {
    const response = await fetch('/pimcore-studio/api/coreshop/grid/apply-action', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        actionId,
        ids: JSON.stringify(ids)
      })
    })

    if (!response.ok) {
      throw new Error(`Failed to apply grid action: ${response.statusText}`)
    }

    return await response.json()
  } catch (err) {
    console.error('[CoreShop Grid] Failed to apply action:', err)
    return {
      success: false,
      message: err instanceof Error ? err.message : 'Unknown error occurred'
    }
  }
}

/**
 * Clear cached filters and/or actions
 * Call this when data may have changed (e.g., after creating/deleting items)
 */
export const clearGridCache = (listType?: string): void => {
  if (listType) {
    filtersCache.delete(listType)
    actionsCache.delete(listType)
  } else {
    filtersCache.clear()
    actionsCache.clear()
  }
}
