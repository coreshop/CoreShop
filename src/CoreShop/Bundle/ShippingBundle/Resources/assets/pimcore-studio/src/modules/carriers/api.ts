/**
 * CoreShop ShippingBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { EntityApi } from '@coreshop/resource/src/entities'

export interface CarrierTranslation {
  locale: string
  title: string
  description?: string
}

export interface ShippingRuleAssignment {
  id?: number
  shippingRule: number
  priority: number
  stopPropagation: boolean
}

export interface CarrierDetail extends Record<string, any> {
  id?: number
  identifier: string
  name?: string // fallback name
  trackingUrl?: string
  logo?: number // asset ID
  translations?: Record<string, CarrierTranslation>
  taxCalculationStrategy?: string
  hideFromCheckout?: boolean
  shippingRules?: ShippingRuleAssignment[]
  // Extensions from CoreBundle
  stores?: number[]
  taxRule?: number
}

export interface CarrierConfig {
  taxCalculationStrategies: Array<{ value: string, label: string }>
}

export class CarrierApi extends EntityApi<CarrierDetail> {
  async getConfig(): Promise<CarrierConfig> {
    const cfg = (this as any).cfg
    const response = await fetch(`${cfg.basePath}${cfg.resourcePath}/get-config`, {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch carrier config')
    }

    return response.json()
  }
}

export const carrierApi = new CarrierApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/carriers'
})
