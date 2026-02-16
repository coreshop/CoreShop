/**
 * CoreShop IndexBundle Index API
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

/**
 * Index Entity
 */
export interface Index {
  id?: number
  name: string
  class?: string
  worker?: string
  indexLastVersion?: boolean
  columns?: IndexColumn[]
  configuration?: Record<string, any>
}

/**
 * Index Column Entity
 */
export interface IndexColumn {
  id?: number
  name: string
  objectKey: string
  objectType?: string
  dataType?: string
  columnType?: string
  getter?: string
  getterConfig?: Record<string, any>
  interpreter?: string
  interpreterConfig?: Record<string, any>
  configuration?: Record<string, any>
}

/**
 * Index Configuration
 */
export interface IndexConfig {
  success: boolean
  workers?: Array<{ type: string; name: string; blockPrefix?: string }>
  workerTypes?: string[] | Record<string, string>
  classes: Array<{ name: string }>
  getters: Array<{ type: string; name: string; blockPrefix?: string }>
  interpreters: Array<{ type: string; name: string; localized?: boolean; relation?: boolean; blockPrefix?: string }>
  fieldTypes: Record<string, Array<{ type: string; name: string }>>
  schemas?: Record<string, any>
  getterSchemaByType?: Record<string, string>
  interpreterSchemaByType?: Record<string, string>
  workerSchemaByType?: Record<string, string>
}

/**
 * Class Definition Field
 */
export interface ClassDefinitionField {
  name: string
  fieldtype: string
  title?: string
  tooltip?: string
  nodeLabel?: string
  nodeType?: string
  childs?: ClassDefinitionField[]
  getter?: string
  interpreter?: string
  configuration?: Record<string, any>
}

/**
 * Class Definition Response
 */
export interface ClassDefinitionResponse {
  fields?: ClassDefinitionField
  systemfields?: ClassDefinitionField
  localizedfields?: ClassDefinitionField
  [key: string]: ClassDefinitionField | undefined
}

/**
 * OpenSearch Client
 */
export interface OpenSearchClient {
  name: string
}

/**
 * Index API - Extends ResourceBundle EntityApi
 */
export class IndexApi extends EntityApi<Index> {
  private readonly basePath: string
  private readonly resourcePath: string

  constructor(config: { basePath: string, resourcePath: string }) {
    super(config)
    this.basePath = config.basePath
    this.resourcePath = config.resourcePath
  }

  /**
   * Get index configuration (workers, classes, getters, interpreters, field types)
   */
  async getConfig(): Promise<IndexConfig> {
    const url = `${this.basePath}${this.resourcePath}/get-config`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error('Failed to fetch index config')
    }

    const config: IndexConfig = await response.json()

    if (config.schemas) {
      preSeedSchemaCache(config.schemas)
    }

    return config
  }

  /**
   * Get class definition for field selection
   */
  async getClassDefinition(className: string): Promise<ClassDefinitionResponse> {
    const url = `${this.basePath}${this.resourcePath}/get-class-definition-for-field-selection?class=${encodeURIComponent(className)}`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error('Failed to fetch class definition')
    }

    return await response.json()
  }

  /**
   * Get available OpenSearch clients
   */
  async getOpenSearchClients(): Promise<Array<OpenSearchClient>> {
    const url = `${this.basePath}${this.resourcePath}/get-open-search-clients`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error('Failed to fetch OpenSearch clients')
    }

    const data = await response.json()
    return data.clients || []
  }
}

/**
 * Index API instance
 */
export const indexApi = new IndexApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/indices'
})