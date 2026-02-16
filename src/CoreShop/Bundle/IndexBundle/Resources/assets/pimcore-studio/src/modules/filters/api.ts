/**
 * CoreShop IndexBundle Filter API
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { EntityApi } from '@coreshop/resource/src/entities/api'
import { preSeedSchemaCache } from '@coreshop/studio-form'
import type { Filter, FilterConfig, IndexField, FieldValue } from './types'

/**
 * Filter API - Extends ResourceBundle EntityApi
 */
export class FilterApi extends EntityApi<Filter> {
  private readonly basePath: string
  private readonly resourcePath: string

  constructor(config: { basePath: string, resourcePath: string }) {
    super(config)
    this.basePath = config.basePath
    this.resourcePath = config.resourcePath
  }

  /**
   * Get filter configuration (available condition types)
   */
  async getConfig(): Promise<FilterConfig> {
    const url = `${this.basePath}${this.resourcePath}/get-config`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error('Failed to fetch filter config')
    }

    const config: FilterConfig = await response.json()

    if (config.schemas) {
      preSeedSchemaCache(config.schemas)
    }

    return config
  }

  /**
   * Get available fields for an index
   */
  async getFieldsForIndex(indexId: number): Promise<IndexField[]> {
    const url = `${this.basePath}${this.resourcePath}/get-fields-for-index?index=${indexId}`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error('Failed to fetch fields for index')
    }

    return await response.json()
  }

  /**
   * Get available values for a filter field (for preSelect dropdowns)
   */
  async getValuesForFilterField(indexId: number, field: string): Promise<FieldValue[]> {
    const url = `${this.basePath}${this.resourcePath}/get-values-for-filter-field?index=${indexId}&field=${encodeURIComponent(field)}`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error('Failed to fetch values for filter field')
    }

    return await response.json()
  }
}

/**
 * Filter API instance
 */
export const filterApi = new FilterApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/filters'
})
