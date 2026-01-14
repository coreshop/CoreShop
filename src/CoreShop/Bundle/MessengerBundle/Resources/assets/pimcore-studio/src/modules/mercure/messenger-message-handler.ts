/**
 * CoreShop MessengerBundle Mercure Message Handler
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { messengerEventEmitter, type MessengerUpdateEvent } from './messenger-event-emitter'

// Message structure from GlobalMessageBus
interface MercureMessage {
  type: 'update' | 'error' | 'cancel'
  payload: unknown
  event: MessageEvent
}

export class MessengerMessageHandler {
  private readonly handlerId = 'coreshop-messenger-handler'

  getId(): string {
    return this.handlerId
  }

  shouldHandle(message: MercureMessage): boolean {
    console.debug('MessengerMessageHandler: shouldHandle called', message)

    if (message.type !== 'update' || !message.payload) {
      console.debug('MessengerMessageHandler: rejected - wrong type or no payload')
      return false
    }

    const payload = message.payload as Record<string, unknown>
    const shouldHandle = payload.eventType === 'coreshop.messenger.update'
    console.debug('MessengerMessageHandler: shouldHandle result', shouldHandle, payload.eventType)
    return shouldHandle
  }

  async handleMessage(message: MercureMessage): Promise<void> {
    console.debug('MessengerMessageHandler: handleMessage called', message)

    if (!message.payload) {
      return
    }

    const event = message.payload as MessengerUpdateEvent
    console.debug('MessengerMessageHandler: emitting event', event)
    messengerEventEmitter.emit(event)
  }

  onRegister(): void {
    console.debug('MessengerMessageHandler: Registered')
  }

  onUnregister(): void {
    console.debug('MessengerMessageHandler: Unregistered')
  }
}
