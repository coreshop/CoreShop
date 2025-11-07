/**
 * CoreShop IndexBundle Worker Configurator Registry
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

export interface WorkerConfiguratorProps {
  configuration: Record<string, any>
  onChange: (configuration: Record<string, any>) => void
}

type WorkerConfiguratorComponent = React.ComponentType<WorkerConfiguratorProps>

@injectable()
export class WorkerConfiguratorRegistry {
  private configurators: Map<string, WorkerConfiguratorComponent> = new Map()

  register(workerType: string, component: WorkerConfiguratorComponent): void {
    this.configurators.set(workerType, component)
  }

  get(workerType: string): WorkerConfiguratorComponent | undefined {
    return this.configurators.get(workerType)
  }

  has(workerType: string): boolean {
    return this.configurators.has(workerType)
  }

  getAll(): Map<string, WorkerConfiguratorComponent> {
    return this.configurators
  }
}
