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
 * Fetch a form schema by block prefix.
 *
 * Uses module-level caching to prevent duplicate API calls
 * when multiple components request the same schema.
 */
export const fetchFormSchema = async (blockPrefix: string): Promise<FormSchemaResponse> => {
  // Return from cache if available
  const cached = schemaCache.get(blockPrefix)
  if (cached) {
    return cached
  }

  // Return existing pending request if already loading
  const pending = pendingRequests.get(blockPrefix)
  if (pending) {
    return pending
  }

  // Start new request
  const request = (async () => {
    try {
      const response = await fetch(
        `/pimcore-studio/api/coreshop-studio-form/schema/${encodeURIComponent(blockPrefix)}`,
      )

      if (!response.ok) {
        throw new Error(`Failed to fetch form schema for "${blockPrefix}": ${response.statusText}`)
      }

      const schema: FormSchemaResponse = await response.json()
      schemaCache.set(blockPrefix, schema)

      return schema
    } catch (err) {
      console.error(`[StudioForm] Failed to load schema for "${blockPrefix}":`, err)
      throw err
    } finally {
      pendingRequests.delete(blockPrefix)
    }
  })()

  pendingRequests.set(blockPrefix, request)

  return request
}

/**
 * Pre-seed the schema cache with schemas from a getConfig response.
 *
 * This eliminates the need for separate HTTP requests for each
 * condition/action schema - they are all included in the getConfig response.
 */
export const preSeedSchemaCache = (schemas: Record<string, FormSchemaResponse>): void => {
  for (const [key, schema] of Object.entries(schemas)) {
    schemaCache.set(key, schema)
  }
}

/**
 * Clear cached schema for a specific block prefix, or all schemas.
 */
export const clearSchemaCache = (blockPrefix?: string): void => {
  if (blockPrefix) {
    schemaCache.delete(blockPrefix)
    pendingRequests.delete(blockPrefix)
  } else {
    schemaCache.clear()
    pendingRequests.clear()
  }
}
