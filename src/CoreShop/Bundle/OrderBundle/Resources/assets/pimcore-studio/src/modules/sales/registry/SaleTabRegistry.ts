/**
 * CoreShop OrderBundle Sale Tab Registry
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
import type React from 'react'
import type { Sale, SaleType } from '../types'

/**
 * Props passed to each tab component
 * All props are now optional as they are provided via SaleContext
 * @deprecated Use useSaleContext() hook instead of props
 */
export interface SaleTabProps {
  // These props are kept for backwards compatibility but are deprecated
  // Use useSaleContext() hook to access sale data and actions
}

/**
 * Block position in the layout
 */
export type BlockPosition = 'top' | 'left' | 'right' | 'bottom'

/**
 * Block configuration (previously Tab)
 */
export interface SaleTab {
  key: string
  label: string
  icon?: string
  priority: number
  position: BlockPosition  // Where to render the block
  types: SaleType[]  // Which sale types should show this block
  component: React.ComponentType<SaleTabProps>
  toolbarButtons?: React.ComponentType[]  // Optional toolbar button components
}

/**
 * Registry for managing sale detail tabs
 *
 * Allows bundles to register custom tabs that will be displayed
 * in the sale detail view based on the sale type.
 *
 * Pattern follows the same approach as ConditionRegistry/ActionRegistry.
 */
@injectable()
export class SaleTabRegistry {
  private tabs: Map<string, SaleTab> = new Map()

  /**
   * Register a tab
   */
  register(key: string, tab: SaleTab): void {
    this.tabs.set(key, tab)
  }

  /**
   * Get a specific tab by key
   */
  get(key: string): SaleTab | undefined {
    return this.tabs.get(key)
  }

  /**
   * Check if a tab exists
   */
  has(key: string): boolean {
    return this.tabs.has(key)
  }

  /**
   * Get all registered tabs
   */
  getAll(): SaleTab[] {
    return Array.from(this.tabs.values())
  }

  /**
   * Get tabs for a specific sale type, sorted by priority
   */
  getForType(type: SaleType): SaleTab[] {
    return Array.from(this.tabs.values())
      .filter(tab => tab.types.includes(type))
      .sort((a, b) => a.priority - b.priority)
  }

  /**
   * Remove a tab
   */
  unregister(key: string): void {
    this.tabs.delete(key)
  }

  /**
   * Clear all tabs
   */
  clear(): void {
    this.tabs.clear()
  }
}
