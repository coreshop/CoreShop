/**
 * CoreShop PaymentBundle - Payment Provider API
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { EntityApi } from '@coreshop/resource/src/entities/api'

export interface GatewayConfig {
  factoryName: string
  gatewayName: string
  config: any[]
  decryptedConfig: any
  id: number
}

export interface PaymentProvider {
  id?: number
  identifier?: string
  active?: string | boolean
  gatewayConfig?: GatewayConfig
}

class PaymentProviderApi extends EntityApi<PaymentProvider> {}

export const paymentProviderApi = new PaymentProviderApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/payment_providers'
})
