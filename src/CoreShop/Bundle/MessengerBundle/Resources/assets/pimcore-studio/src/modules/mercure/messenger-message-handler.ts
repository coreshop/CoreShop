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

import {messengerEventEmitter, type MessengerUpdateEvent} from './messenger-event-emitter'

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
    if (message.type !== 'update' || !message.payload) {
      return false
    }

    const payload = message.payload as Record<string, unknown>
    return payload.eventType === 'coreshop.messenger.update'
  }

  async handleMessage(message: MercureMessage): Promise<void> {
    if (!message.payload) {
      return
    }

    const event = message.payload as MessengerUpdateEvent

    messengerEventEmitter.emit(event)
  }

  onRegister(): void {
  }

  onUnregister(): void {
  }
}
