/**
 * CoreShop PimcoreBundle Grid Filter Context
 *
 * Context provider for managing grid filter state across components.
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
import { coreshopBroker } from '../../broker'
import { GRID_EVENTS } from '../events'

interface GridFilterContextValue {
  listType: string
  selectedFilter: string | null
  setSelectedFilter: (filterId: string | null) => void
}

const GridFilterContext = React.createContext<GridFilterContextValue | null>(null)

interface GridFilterProviderProps {
  listType: string
  children: React.ReactNode
}

/**
 * Provider component for grid filter state
 */
export const GridFilterProvider: React.FC<GridFilterProviderProps> = ({
  listType,
  children
}) => {
  const [selectedFilter, setSelectedFilterState] = React.useState<string | null>(null)

  const setSelectedFilter = React.useCallback((filterId: string | null) => {
    setSelectedFilterState(filterId)

    // Fire broker event for extensibility
    coreshopBroker.fireEvent(GRID_EVENTS.FILTER_CHANGED, {
      listType,
      filterId
    })
  }, [listType])

  const value = React.useMemo(() => ({
    listType,
    selectedFilter,
    setSelectedFilter
  }), [listType, selectedFilter, setSelectedFilter])

  return (
    <GridFilterContext.Provider value={value}>
      {children}
    </GridFilterContext.Provider>
  )
}

/**
 * Hook to access grid filter context
 */
export const useGridFilterContext = (): GridFilterContextValue => {
  const context = React.useContext(GridFilterContext)
  if (!context) {
    throw new Error('useGridFilterContext must be used within a GridFilterProvider')
  }
  return context
}

/**
 * Hook to access grid filter context (returns null if not in provider)
 */
export const useGridFilterContextOptional = (): GridFilterContextValue | null => {
  return React.useContext(GridFilterContext)
}
