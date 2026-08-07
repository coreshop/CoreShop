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

import { injectable } from 'inversify'
import type { ResourceConfig } from './types'
import { ResourceConfigApi } from './api'

@injectable()
export class ResourceConfigProvider {
  private config: ResourceConfig | null = null
  private loading: Promise<ResourceConfig> | null = null
  private readonly api: ResourceConfigApi

  constructor() {
    this.api = new ResourceConfigApi()
  }

  /**
   * Get the resource configuration. Loads it from the API if not already cached.
   */
  async getConfig(): Promise<ResourceConfig> {
    if (this.config) {
      return this.config
    }

    if (this.loading) {
      return this.loading
    }

    this.loading = this.api.getConfig().then(config => {
      this.config = config
      this.loading = null
      return config
    })

    return this.loading
  }

  /**
   * Check if a Pimcore object class is allowed for a given CoreShop resource type.
   * @param resourceType The CoreShop resource type (e.g., 'coreshop.customer', 'coreshop.product')
   * @param className The Pimcore DataObject class name (e.g., 'CoreShopCustomer', 'CoreShopProduct')
   */
  async isClassAllowedForResource(resourceType: string, className: string): Promise<boolean> {
    const config = await this.getConfig()

    // Parse resource type (e.g., 'coreshop.customer' -> ['coreshop', 'customer'])
    const parts = resourceType.split('.')
    if (parts.length !== 2) {
      return false
    }

    const [namespace, type] = parts

    // Check if the namespace exists in the stack
    const namespaceStack = config.stack[namespace as keyof typeof config.stack]
    if (!namespaceStack) {
      return false
    }

    // Get the allowed classes for this resource type
    const allowedClasses = namespaceStack[type]
    if (!allowedClasses) {
      return false
    }

    // Check if the class is in the allowed list
    return allowedClasses.includes(className)
  }

  /**
   * Get all allowed classes for a given CoreShop resource type.
   * @param resourceType The CoreShop resource type (e.g., 'coreshop.customer', 'coreshop.product')
   */
  async getAllowedClasses(resourceType: string): Promise<string[]> {
    const config = await this.getConfig()

    const parts = resourceType.split('.')
    if (parts.length !== 2) {
      return []
    }

    const [namespace, type] = parts
    const namespaceStack = config.stack[namespace as keyof typeof config.stack]
    if (!namespaceStack) {
      return []
    }

    return namespaceStack[type] || []
  }

  /**
   * Clear the cached configuration. Useful for testing or forcing a reload.
   */
  clearCache(): void {
    this.config = null
    this.loading = null
  }
}
