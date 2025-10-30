/**
 * CoreShop RuleBundle Studio Plugin
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
import type { ActionComponentProps } from '../types'

type ActionComponent = React.ComponentType<ActionComponentProps>

export class ActionRegistry {
  private actions: Map<string, ActionComponent> = new Map()

  register(type: string, component: ActionComponent): void {
    this.actions.set(type, component)
  }

  get(type: string): ActionComponent | undefined {
    return this.actions.get(type)
  }

  has(type: string): boolean {
    return this.actions.has(type)
  }

  getAll(): Map<string, ActionComponent> {
    return this.actions
  }
}
