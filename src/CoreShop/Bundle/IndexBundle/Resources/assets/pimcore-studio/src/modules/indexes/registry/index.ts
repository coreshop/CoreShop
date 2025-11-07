/**
 * CoreShop IndexBundle Getter/Interpreter Registry
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { ReactNode } from 'react'

/**
 * Configuration component props
 */
export interface ConfigComponentProps {
  value?: Record<string, any>
  onChange: (value: Record<string, any>) => void
}

/**
 * Getter/Interpreter Configurator
 */
export interface Configurator {
  type: string
  component: (props: ConfigComponentProps) => ReactNode
}

/**
 * Registry for Getter Configurators
 */
export class GetterConfiguratorRegistry {
  private configurators: Map<string, Configurator> = new Map()

  register(type: string, component: (props: ConfigComponentProps) => ReactNode): void {
    this.configurators.set(type, { type, component })
  }

  get(type: string): Configurator | undefined {
    return this.configurators.get(type)
  }

  has(type: string): boolean {
    return this.configurators.has(type)
  }

  getAll(): Configurator[] {
    return Array.from(this.configurators.values())
  }
}

/**
 * Registry for Interpreter Configurators
 */
export class InterpreterConfiguratorRegistry {
  private configurators: Map<string, Configurator> = new Map()

  register(type: string, component: (props: ConfigComponentProps) => ReactNode): void {
    this.configurators.set(type, { type, component })
  }

  get(type: string): Configurator | undefined {
    return this.configurators.get(type)
  }

  has(type: string): boolean {
    return this.configurators.has(type)
  }

  getAll(): Configurator[] {
    return Array.from(this.configurators.values())
  }
}

/**
 * Registry for Worker Configurators
 */
export class WorkerConfiguratorRegistry {
  private configurators: Map<string, Configurator> = new Map()

  register(type: string, component: (props: ConfigComponentProps) => ReactNode): void {
    this.configurators.set(type, { type, component })
  }

  get(type: string): Configurator | undefined {
    return this.configurators.get(type)
  }

  has(type: string): boolean {
    return this.configurators.has(type)
  }

  getAll(): Configurator[] {
    return Array.from(this.configurators.values())
  }
}

/**
 * Singleton instances
 */
export const getterConfiguratorRegistry = new GetterConfiguratorRegistry()
export const interpreterConfiguratorRegistry = new InterpreterConfiguratorRegistry()
export const workerConfiguratorRegistry = new WorkerConfiguratorRegistry()
