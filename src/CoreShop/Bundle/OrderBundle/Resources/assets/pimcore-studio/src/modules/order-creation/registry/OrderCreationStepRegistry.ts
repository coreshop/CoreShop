/**
 * CoreShop OrderBundle - Order Creation Step Registry
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
import type { OrderCreationStepConfig } from '../types'

/**
 * Registry for managing order creation wizard steps
 *
 * Allows bundles to register custom steps that will be displayed
 * in the order creation wizard.
 *
 * Pattern follows the same approach as SaleTabRegistry.
 */
@injectable()
export class OrderCreationStepRegistry {
  private steps: Map<string, OrderCreationStepConfig> = new Map()

  /**
   * Register a step
   */
  register(key: string, config: OrderCreationStepConfig): void {
    this.steps.set(key, config)
  }

  /**
   * Get a specific step by key
   */
  get(key: string): OrderCreationStepConfig | undefined {
    return this.steps.get(key)
  }

  /**
   * Check if a step exists
   */
  has(key: string): boolean {
    return this.steps.has(key)
  }

  /**
   * Get all registered steps
   */
  getAll(): OrderCreationStepConfig[] {
    return Array.from(this.steps.values())
  }

  /**
   * Get all steps sorted by priority (lower = first)
   */
  getSorted(): OrderCreationStepConfig[] {
    return this.getAll().sort((a, b) => a.priority - b.priority)
  }

  /**
   * Remove a step
   */
  unregister(key: string): void {
    this.steps.delete(key)
  }

  /**
   * Clear all steps
   */
  clear(): void {
    this.steps.clear()
  }
}
