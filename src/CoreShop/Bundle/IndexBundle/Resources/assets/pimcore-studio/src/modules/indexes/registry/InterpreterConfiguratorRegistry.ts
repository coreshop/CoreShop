/**
 * CoreShop IndexBundle Interpreter Configurator Registry
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
import React from 'react'

export interface InterpreterConfiguratorProps {
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

type InterpreterConfiguratorComponent = React.ComponentType<InterpreterConfiguratorProps>

@injectable()
export class InterpreterConfiguratorRegistry {
  private configurators: Map<string, InterpreterConfiguratorComponent> = new Map()

  register(type: string, component: InterpreterConfiguratorComponent): void {
    this.configurators.set(type, component)
  }

  get(type: string): InterpreterConfiguratorComponent | undefined {
    return this.configurators.get(type)
  }

  has(type: string): boolean {
    return this.configurators.has(type)
  }

  getAll(): Map<string, InterpreterConfiguratorComponent> {
    return this.configurators
  }
}
