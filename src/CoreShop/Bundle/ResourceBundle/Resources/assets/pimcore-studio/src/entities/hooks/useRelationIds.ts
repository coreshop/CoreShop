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

import { useState, useEffect, useRef } from 'react'
import type { ManyToManyRelationValue } from '../types/relation'
import { loadElementDetails } from '@coreshop/pimcore/src/api/helperApi'

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
  const relationValueRef = useRef<ManyToManyRelationValue | null>(null)

  useEffect(() => {
    relationValueRef.current = relationValue
  }, [relationValue])

  // Convert backend format (string IDs) to ManyToManyRelationValue format
  useEffect(() => {
    let cancelled = false

    if (!ids || (Array.isArray(ids) && ids.length === 0)) {
      setLoading(false)
      setRelationValue(null)
      return () => {
        cancelled = true
      }
    }

    // If already in ManyToManyRelationValue format, sanitize and filter items
    if (Array.isArray(ids) && ids.length > 0 && typeof ids[0] === 'object') {
      const items = ids as ManyToManyRelationValue

      const sanitizedItems = sanitizeRelationItems(items, entityName, elementType)

      if (!cancelled) {
        setLoading(false)
        setRelationValue(sanitizedItems.length > 0 ? sanitizedItems : null)
      }
      return () => {
        cancelled = true
      }
    }

    // Convert string IDs to ManyToManyRelationValue format by loading details from API
    if (Array.isArray(ids) && ids.length > 0 && (typeof ids[0] === 'string' || typeof ids[0] === 'number')) {
      const normalizedIds = (ids as Array<string | number>).map(String)
      const currentById = new Map(
        (relationValueRef.current ?? []).map(item => [String(item.id), item] as const)
      )

      const fromCurrent = normalizedIds
        .map(id => currentById.get(id))
        .filter((item): item is ManyToManyRelationValue[number] => item != null)

      if (fromCurrent.length === normalizedIds.length) {
        const sanitizedCurrent = sanitizeRelationItems(fromCurrent, entityName, elementType)
        setLoading(false)
        setRelationValue(sanitizedCurrent.length > 0 ? sanitizedCurrent : null)
        return () => {
          cancelled = true
        }
      }

      setLoading(true)
      loadElementDetails(normalizedIds, elementType)
        .then(details => {
          const converted = normalizedIds.map(id => {
            const currentItem = currentById.get(id)
            if (currentItem) {
              return currentItem
            }

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
          const sanitized = sanitizeRelationItems(converted, entityName, elementType)
          if (!cancelled) {
            setRelationValue(sanitized.length > 0 ? sanitized : null)
          }
        })
        .catch(error => {
          console.error('Failed to load element details:', error)
          // Fallback to simple format on error
          const converted = normalizedIds.map(id => ({
            id: parseInt(id),
            type: elementType,
            fullPath: `${entityName} ${id}`,
              subtype: null,
              isPublished: true
          }))
          const sanitized = sanitizeRelationItems(converted, entityName, elementType)
          if (!cancelled) {
            setRelationValue(sanitized.length > 0 ? sanitized : null)
          }
        })
        .finally(() => {
          if (!cancelled) {
            setLoading(false)
          }
        })
    } else {
      // Unsupported shape
      setLoading(false)
      setRelationValue(null)
    }

    return () => {
      cancelled = true
    }
  }, [ids, entityName, elementType])

  // Handler that converts ManyToManyRelationValue back to string IDs for backend
  const handleChange = (value: ManyToManyRelationValue | null): string[] => {
    const sanitized = sanitizeRelationItems(value ?? [], entityName, elementType)
    relationValueRef.current = sanitized.length > 0 ? sanitized : null
    setRelationValue(sanitized.length > 0 ? sanitized : null)
    return sanitized.map(item => String(item.id))
  }

  return [relationValue, handleChange, loading]
}

const sanitizeRelationItems = (
  items: Array<Partial<ManyToManyRelationValue[number]> | null | undefined>,
  entityName: string,
  elementType: string
): ManyToManyRelationValue => {
  return items
    .map((item) => {
      if (item == null || item.id == null) {
        return null
      }

      const numericId = typeof item.id === 'number' ? item.id : Number(item.id)
      if (!Number.isFinite(numericId)) {
        return null
      }

      return {
        id: numericId,
        type: (typeof item.type === 'string' && item.type.length > 0) ? item.type : elementType,
        fullPath: (typeof item.fullPath === 'string' && item.fullPath.length > 0)
          ? item.fullPath
          : `${entityName} ${String(numericId)}`,
        subtype: item.subtype ?? null,
        isPublished: item.isPublished ?? true,
      }
    })
    .filter((item): item is ManyToManyRelationValue[number] => item != null)
}
