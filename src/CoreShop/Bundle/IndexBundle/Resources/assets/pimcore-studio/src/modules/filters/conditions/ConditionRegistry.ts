/**
 * CoreShop IndexBundle Filter Condition Registry
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
import type { ConditionProps } from '../types'

/**
 * Filter Condition Registry
 * Similar to RuleBundle's ConditionRegistry but for filter conditions
 */
@injectable()
export class ConditionRegistry {
  private conditions: Map<string, React.ComponentType<ConditionProps>> = new Map()

  /**
   * Register a condition component
   */
  register(type: string, component: React.ComponentType<ConditionProps>): void {
    this.conditions.set(type, component)
  }

  /**
   * Get a condition component by type
   */
  get(type: string): React.ComponentType<ConditionProps> | undefined {
    return this.conditions.get(type)
  }

  /**
   * Check if a condition type exists
   */
  has(type: string): boolean {
    return this.conditions.has(type)
  }

  /**
   * Get all registered condition types
   */
  getAll(): Array<[string, React.ComponentType<ConditionProps>]> {
    return Array.from(this.conditions.entries())
  }
}
