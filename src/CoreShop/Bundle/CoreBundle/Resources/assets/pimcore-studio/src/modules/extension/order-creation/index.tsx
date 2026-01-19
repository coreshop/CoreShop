/**
 * CoreShop CoreBundle - Order Creation Extension Module
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { OrderCreationStepRegistry } from '@coreshop/order/src/modules/order-creation/registry'
import { orderCreationServiceIds } from '@coreshop/order/src/modules/order-creation/service-ids'
import { AddressStepConfig, ShippingStepConfig } from './steps'

/**
 * Wait for OrderCreationStepRegistry to be available
 * This is needed because OrderBundle may initialize after CoreBundle
 */
async function waitForOrderCreationRegistry(
  maxAttempts = 50,
  interval = 100
): Promise<boolean> {
  let attempts = 0
  return new Promise((resolve) => {
    const check = (): void => {
      attempts++
      if (container.isBound(orderCreationServiceIds.stepRegistry)) {
        resolve(true)
        return
      }
      if (attempts >= maxAttempts) {
        console.warn('[CoreShop Core] Timeout waiting for OrderCreationStepRegistry')
        resolve(false)
        return
      }
      setTimeout(check, interval)
    }
    check()
  })
}

export const OrderCreationExtensionModule: AbstractModule = {
  async onInit(): Promise<void> {
    const registryAvailable = await waitForOrderCreationRegistry()

    if (!registryAvailable) {
      console.warn('[CoreShop Core] OrderCreationStepRegistry not available, skipping registration')
      return
    }

    const registry = container.get<OrderCreationStepRegistry>(
      orderCreationServiceIds.stepRegistry
    )

    // Register CoreBundle steps (extensions)
    registry.register('address', AddressStepConfig)
    registry.register('shipping', ShippingStepConfig)
  }
}
