/**
 * Sale Tab Extension Module
 *
 * This module extends the CreateShipmentModal from OrderBundle with carrier selection.
 * Uses the ModalFieldExtensionRegistry to inject additional fields.
 *
 * Uses lazy initialization to handle async bundle loading via Module Federation.
 */

import React from 'react'
import { Form } from 'antd'
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { ModalFieldExtensionRegistry } from '@coreshop/order/src/modules/sales/extensions'
import { extensionServiceIds } from '@coreshop/order/src/modules/sales/extensions/service-ids'
import { CarrierSelect } from '@coreshop/shipping/src/components/CarrierSelect'

/**
 * Wait for modal field extension registry to be available
 */
async function waitForModalFieldExtensionRegistry(maxAttempts: number = 50, interval: number = 100): Promise<void> {
  let attempts = 0

  return new Promise((resolve) => {
    const checkRegistry = () => {
      attempts++

      if (container.isBound(extensionServiceIds.modalFieldExtensionRegistry)) {
        resolve()
        return
      }

      if (attempts >= maxAttempts) {
        console.warn(
          `[CoreShop Core] Timeout waiting for ModalFieldExtensionRegistry after ${maxAttempts} attempts`
        )
        resolve()
        return
      }

      setTimeout(checkRegistry, interval)
    }

    checkRegistry()
  })
}

/**
 * Register field extensions into ModalFieldExtensionRegistry
 */
function registerModalFieldExtensions(): void {
  if (!container.isBound(extensionServiceIds.modalFieldExtensionRegistry)) {
    console.warn('[CoreShop Core] ModalFieldExtensionRegistry not available, skipping field registration')
    return
  }

  try {
    const registry = container.get<ModalFieldExtensionRegistry>(extensionServiceIds.modalFieldExtensionRegistry)

    // Register carrier field for CreateShipmentModal
    registry.register('create-shipment', ({ form, carrierId }) => (
      <Form.Item
        label="Carrier"
        name="carrier"
        initialValue={carrierId}
        rules={[{ required: true, message: 'Please select a carrier' }]}
      >
        <CarrierSelect
          placeholder="Select a carrier"
          style={{ width: '100%' }}
        />
      </Form.Item>
    ))
  } catch (error) {
    console.error('[CoreShop Core] Failed to register modal field extensions:', error)
  }
}

/**
 * Sale Tab Extension Module
 */
export const SaleTabExtensionModule: AbstractModule = {
  async onInit(): Promise<void> {
    // Wait for registry to be available
    await waitForModalFieldExtensionRegistry()

    // Register field extensions
    registerModalFieldExtensions()
  }
}
