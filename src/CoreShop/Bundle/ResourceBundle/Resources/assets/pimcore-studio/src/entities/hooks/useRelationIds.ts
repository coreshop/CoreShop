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
import type { ManyToManyRelationValue } from '../types/relation'
import { loadElementDetails } from '../api/helperApi'

/**
 * Hook to convert between backend format (string IDs) and ManyToManyRelation format
 * Automatically loads element details (fullPath, subtype, etc.) from the API
 *
 * @param ids - Array of string IDs from backend or ManyToManyRelationValue
 * @param entityName - Name to display for entities (e.g., 'Product', 'Category') - used as fallback
 * @param elementType - Type of element for API ('object', 'asset', 'document')
 * @returns Tuple of [relationValue, handleChange, loading]
 */
export function useRelationIds(
  ids: string[] | ManyToManyRelationValue | undefined,
  entityName: string = 'Entity',
  elementType: string = 'object'
): [ManyToManyRelationValue | null, (value: ManyToManyRelationValue | null) => string[], boolean] {
  const [relationValue, setRelationValue] = useState<ManyToManyRelationValue | null>(null)
  const [loading, setLoading] = useState<boolean>(false)

  // Convert backend format (string IDs) to ManyToManyRelationValue format
  useEffect(() => {
    if (!ids) {
      setRelationValue(null)
      return
    }

    // If already in ManyToManyRelationValue format, use as-is
    if (Array.isArray(ids) && ids.length > 0 && typeof ids[0] === 'object') {
      setRelationValue(ids as ManyToManyRelationValue)
      return
    }

    // Convert string IDs to ManyToManyRelationValue format by loading details from API
    if (Array.isArray(ids) && ids.length > 0 && typeof ids[0] === 'string') {
      setLoading(true)
      loadElementDetails(ids as string[], elementType)
        .then(details => {
          const converted = ids.map(id => {
            const detail = details[id]
            if (detail) {
              return detail
            }
            // Fallback if API didn't return this ID
            return {
              id: parseInt(id),
              type: elementType,
              fullPath: `${entityName} ${id}`,
              subtype: null,
              isPublished: true
            }
          })
          setRelationValue(converted)
        })
        .catch(error => {
          console.error('Failed to load element details:', error)
          // Fallback to simple format on error
          const converted = ids.map(id => ({
            id: parseInt(id),
            type: elementType,
            fullPath: `${entityName} ${id}`,
            subtype: null,
            isPublished: true
          }))
          setRelationValue(converted)
        })
        .finally(() => {
          setLoading(false)
        })
      return
    }

    // Empty array
    if (Array.isArray(ids) && ids.length === 0) {
      setRelationValue(null)
    }
  }, [ids, entityName, elementType])

  // Handler that converts ManyToManyRelationValue back to string IDs for backend
  const handleChange = (value: ManyToManyRelationValue | null): string[] => {
    setRelationValue(value)
    return value?.map(item => String(item.id)) || []
  }

  return [relationValue, handleChange, loading]
}
