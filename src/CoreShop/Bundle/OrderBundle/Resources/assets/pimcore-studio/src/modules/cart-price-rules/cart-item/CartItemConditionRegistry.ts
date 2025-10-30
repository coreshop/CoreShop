/**
 * CoreShop OrderBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules'

type CartItemConditionComponent = React.ComponentType<ConditionComponentProps>

export class CartItemConditionRegistry {
  private conditions: Map<string, CartItemConditionComponent> = new Map()

  register(type: string, component: CartItemConditionComponent): void {
    this.conditions.set(type, component)
  }

  get(type: string): CartItemConditionComponent | undefined {
    return this.conditions.get(type)
  }

  has(type: string): boolean {
    return this.conditions.has(type)
  }

  getAll(): Map<string, CartItemConditionComponent> {
    return this.conditions
  }
}
