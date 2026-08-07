/**
 * CoreShop ResourceBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { useState, useEffect } from 'react'
import type { EntityApi } from '../api'

/**
 * Interface for entities that can be used in select components
 * Must have an id field
 */
export interface SelectableEntity {
  id: number
  [key: string]: any
}

/**
 * Option format for Ant Design Select component
 */
export interface SelectOption {
  label: string
  value: number
}

/**
 * Hook to load entities from API and prepare them for use in Select components
 *
 * @param api - EntityApi instance to load entities from
 * @param selectedIds - Currently selected entity IDs (can be numbers or strings from backend)
 * @param labelKey - The key to use for the label (defaults to 'name')
 * @returns Tuple of [options, value, handleChange, loading]
 */
export function useEntitySelect<T extends SelectableEntity>(
  api: EntityApi<T>,
  selectedIds: number[] | string[] | undefined,
  labelKey: string = 'name'
): [SelectOption[], number[], (ids: number[]) => void, boolean] {
  const [options, setOptions] = useState<SelectOption[]>([])
  const [loading, setLoading] = useState<boolean>(false)

  // Normalize selectedIds to number[] (backend may send strings)
  const normalizedIds = selectedIds?.map(id => typeof id === 'string' ? parseInt(id, 10) : id) || []
  const [value, setValue] = useState<number[]>(normalizedIds)

  // Load entities from API on mount
  useEffect(() => {
    setLoading(true)
    api.list()
      .then(response => {
        // EntityListResponse is an Array, not an object with data property
        const entityOptions = response.map(entity => ({
          label: entity[labelKey] || `Entity ${entity.id}`,
          value: entity.id
        }))
        setOptions(entityOptions)
      })
      .catch(error => {
        console.error('Failed to load entities:', error)
        setOptions([])
      })
      .finally(() => {
        setLoading(false)
      })
  }, [api, labelKey])

  // Update value when selectedIds change
  useEffect(() => {
    const normalized = selectedIds?.map(id => typeof id === 'string' ? parseInt(id, 10) : id) || []
    setValue(normalized)
  }, [selectedIds])

  // Handler to update value
  const handleChange = (ids: number[]): void => {
    setValue(ids)
  }

  return [options, value, handleChange, loading]
}
