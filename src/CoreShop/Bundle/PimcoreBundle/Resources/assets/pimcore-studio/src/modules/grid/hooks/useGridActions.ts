/**
 * CoreShop PimcoreBundle useGridActions Hook
 *
 * Hook for loading and managing grid actions for a specific list type.
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
import { fetchGridActions, applyGridAction, type GridAction, type ActionResult } from '../api'

interface UseGridActionsResult {
  actions: GridAction[]
  loading: boolean
  error: Error | null
  executing: boolean
  executeAction: (actionId: string, ids: number[]) => Promise<ActionResult>
  reload: () => Promise<void>
}

/**
 * Hook to load grid actions for a specific list type
 *
 * @param listType - The type of list (e.g., 'coreshop_order', 'coreshop_cart')
 */
export const useGridActions = (listType: string): UseGridActionsResult => {
  const [actions, setActions] = React.useState<GridAction[]>([])
  const [loading, setLoading] = React.useState(true)
  const [error, setError] = React.useState<Error | null>(null)
  const [executing, setExecuting] = React.useState(false)

  const loadActions = React.useCallback(async () => {
    setLoading(true)
    setError(null)

    try {
      const data = await fetchGridActions(listType)
      setActions(data)
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Failed to load actions'))
    } finally {
      setLoading(false)
    }
  }, [listType])

  React.useEffect(() => {
    void loadActions()
  }, [loadActions])

  const executeAction = React.useCallback(async (actionId: string, ids: number[]): Promise<ActionResult> => {
    setExecuting(true)
    try {
      return await applyGridAction(actionId, ids)
    } finally {
      setExecuting(false)
    }
  }, [])

  return {
    actions,
    loading,
    error,
    executing,
    executeAction,
    reload: loadActions
  }
}
