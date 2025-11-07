/**
 * CoreShop IndexBundle Getter Configurator Registry
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

export interface GetterConfiguratorProps {
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

type GetterConfiguratorComponent = React.ComponentType<GetterConfiguratorProps>

@injectable()
export class GetterConfiguratorRegistry {
  private configurators: Map<string, GetterConfiguratorComponent> = new Map()

  register(type: string, component: GetterConfiguratorComponent): void {
    this.configurators.set(type, component)
  }

  get(type: string): GetterConfiguratorComponent | undefined {
    return this.configurators.get(type)
  }

  has(type: string): boolean {
    return this.configurators.has(type)
  }

  getAll(): Map<string, GetterConfiguratorComponent> {
    return this.configurators
  }
}
