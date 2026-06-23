/**
 * CoreShop OrderBundle - Payment Events
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

type PaymentEventHandler = () => void

class PaymentEventEmitter {
  private listeners: Map<string, PaymentEventHandler[]> = new Map()

  on(event: string, handler: PaymentEventHandler): void {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, [])
    }
    this.listeners.get(event)!.push(handler)
  }

  off(event: string, handler: PaymentEventHandler): void {
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

export const paymentEvents = new PaymentEventEmitter()

// Event names
export const PAYMENT_EVENTS = {
  CREATE_PAYMENT: 'create-payment'
}
