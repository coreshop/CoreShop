/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type React from 'react'
import { injectable } from 'inversify'

export interface GatewayConfiguratorProps {
  config: Record<string, any>
  onChange: (config: Record<string, any>) => void
}

export type GatewayConfigurator = React.FC<GatewayConfiguratorProps>

/**
 * Registry for gateway configurators.
 * Each payment gateway (e.g., PayPal, Sofort) can register its own configuration component.
 * Other bundles can register their gateway configurators via the container.
 */
@injectable()
export class GatewayRegistry {
  private readonly configurators: Map<string, GatewayConfigurator> = new Map()

  register(factoryName: string, configurator: GatewayConfigurator): void {
    this.configurators.set(factoryName.toLowerCase(), configurator)
  }

  get(factoryName: string): GatewayConfigurator | undefined {
    return this.configurators.get(factoryName.toLowerCase())
  }

  has(factoryName: string): boolean {
    return this.configurators.has(factoryName.toLowerCase())
  }

  getAll(): Map<string, GatewayConfigurator> {
    return new Map(this.configurators)
  }
}
