/**
 * CoreShop Schema Adapter - API
 *
 * Fetches form schemas from the backend with module-level caching.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { FormSchemaResponse } from './types'

// Module-level cache for schemas
const schemaCache = new Map<string, FormSchemaResponse>()
const pendingRequests = new Map<string, Promise<FormSchemaResponse>>()

/**
 * Fetch a form schema by alias.
 *
 * Uses module-level caching to prevent duplicate API calls
 * when multiple components request the same schema.
 */
export const fetchFormSchema = async (alias: string): Promise<FormSchemaResponse> => {
  // Return from cache if available
  const cached = schemaCache.get(alias)
  if (cached) {
    return cached
  }

  // Return existing pending request if already loading
  const pending = pendingRequests.get(alias)
  if (pending) {
    return pending
  }

  // Start new request
  const request = (async () => {
    try {
      const response = await fetch(
        `/pimcore-studio/api/coreshop-studio-form/schema/${encodeURIComponent(alias)}`,
      )

      if (!response.ok) {
        throw new Error(`Failed to fetch form schema for "${alias}": ${response.statusText}`)
      }

      const schema: FormSchemaResponse = await response.json()
      schemaCache.set(alias, schema)

      return schema
    } catch (err) {
      console.error(`[StudioForm] Failed to load schema for "${alias}":`, err)
      throw err
    } finally {
      pendingRequests.delete(alias)
    }
  })()

  pendingRequests.set(alias, request)

  return request
}

/**
 * Clear cached schema for a specific alias, or all schemas.
 */
export const clearSchemaCache = (alias?: string): void => {
  if (alias) {
    schemaCache.delete(alias)
    pendingRequests.delete(alias)
  } else {
    schemaCache.clear()
    pendingRequests.clear()
  }
}
