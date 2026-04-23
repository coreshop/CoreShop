/**
 * CoreShop MessengerBundle Mercure Event Emitter
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export interface MessengerUpdateEvent {
  eventType: 'coreshop.messenger.update'
  type: 'message_handled' | 'message_failed' | 'message_retried' | 'message_rejected'
  receiverName: string
  messageClass: string
  messageId: string | null
  errorMessage: string | null
  relatedObjectId: number | null
  timestamp: string
}

type MessengerEventListener = (event: MessengerUpdateEvent) => void

class MessengerEventEmitterClass {
  private listeners: Set<MessengerEventListener> = new Set()

  subscribe(listener: MessengerEventListener): () => void {
    this.listeners.add(listener)
    console.debug('MessengerEventEmitter: Subscribed, total listeners:', this.listeners.size)
    return () => {
      this.listeners.delete(listener)
      console.debug('MessengerEventEmitter: Unsubscribed, total listeners:', this.listeners.size)
    }
  }

  emit(event: MessengerUpdateEvent): void {
    console.debug('MessengerEventEmitter: Emitting event to', this.listeners.size, 'listeners', event)
    this.listeners.forEach(listener => {
      try {
        listener(event)
      } catch (error) {
        console.error('MessengerEventEmitter: Error in listener', error)
      }
    })
  }
}

export const messengerEventEmitter = new MessengerEventEmitterClass()
