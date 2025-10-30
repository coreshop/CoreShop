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
import type { ActionComponentProps } from '@coreshop/rule/src/rules'

type CartItemActionComponent = React.ComponentType<ActionComponentProps>

export class CartItemActionRegistry {
  private actions: Map<string, CartItemActionComponent> = new Map()

  register(type: string, component: CartItemActionComponent): void {
    this.actions.set(type, component)
  }

  get(type: string): CartItemActionComponent | undefined {
    return this.actions.get(type)
  }

  has(type: string): boolean {
    return this.actions.has(type)
  }

  getAll(): Map<string, CartItemActionComponent> {
    return this.actions
  }
}
