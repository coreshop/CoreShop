/**
 * CoreShop OrderBundle - Shipment Events
 *
 * Event system to allow toolbar buttons to trigger modal opening
 * without direct access to component state.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

type ShipmentEventHandler = () => void

class ShipmentEventEmitter {
  private listeners: Map<string, ShipmentEventHandler[]> = new Map()

  on(event: string, handler: ShipmentEventHandler): void {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, [])
    }
    this.listeners.get(event)!.push(handler)
  }

  off(event: string, handler: ShipmentEventHandler): void {
    const handlers = this.listeners.get(event)
    if (handlers) {
      const index = handlers.indexOf(handler)
      if (index > -1) {
        handlers.splice(index, 1)
      }
    }
  }

  emit(event: string): void {
    const handlers = this.listeners.get(event)
    if (handlers) {
      handlers.forEach(handler => handler())
    }
  }
}

export const shipmentEvents = new ShipmentEventEmitter()

// Event names
export const SHIPMENT_EVENTS = {
  CREATE_SHIPMENT: 'create-shipment'
}
