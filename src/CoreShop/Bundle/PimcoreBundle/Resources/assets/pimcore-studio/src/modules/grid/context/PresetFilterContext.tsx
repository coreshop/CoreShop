/**
 * CoreShop PimcoreBundle Preset Filter Context
 *
 * React Context for sharing preset filter state between the toolbar and listing.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { createContext, useContext, useState, type ReactNode } from 'react'

interface PresetFilterContextValue {
  selectedFilter: string | null
  listType: string | null
  setSelectedFilter: (filter: string | null) => void
  setListType: (type: string) => void
}

const PresetFilterContext = createContext<PresetFilterContextValue | null>(null)

export interface PresetFilterProviderProps {
  children: ReactNode
  initialListType?: string
}

/**
 * Provider component for preset filter state.
 * Wrap your listing and toolbar in this provider to share filter state.
 */
export const PresetFilterProvider: React.FC<PresetFilterProviderProps> = ({
  children,
  initialListType
}) => {
  const [selectedFilter, setSelectedFilter] = useState<string | null>(null)
  const [listType, setListType] = useState<string | null>(initialListType ?? null)

  return (
    <PresetFilterContext.Provider
      value={{
        selectedFilter,
        listType,
        setSelectedFilter,
        setListType
      }}
    >
      {children}
    </PresetFilterContext.Provider>
  )
}

/**
 * Hook to access the preset filter context.
 * Must be used within a PresetFilterProvider.
 */
export const usePresetFilter = (): PresetFilterContextValue => {
  const context = useContext(PresetFilterContext)

  if (context === null) {
    throw new Error('usePresetFilter must be used within a PresetFilterProvider')
  }

  return context
}

/**
 * Hook to safely access preset filter context (returns null if not in provider).
 * Useful for conditional usage in decorators.
 */
export const usePresetFilterOptional = (): PresetFilterContextValue | null => {
  return useContext(PresetFilterContext)
}
