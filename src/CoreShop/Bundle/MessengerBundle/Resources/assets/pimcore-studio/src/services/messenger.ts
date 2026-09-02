/**
 * CoreShop MessengerService
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import {
  MessengerApiResponse,
  MessengerCountResponse,
  MessengerDeleteResponse,
  MessengerRetryResponse,
  MessengerChartData,
  MessengerMessage,
  MessengerFailedMessage,
  MessengerReceiver
} from '../types'

export class Messenger {
  private baseUrl = '/pimcore-studio/api/coreshop'

  /**
   * Get message count statistics for chart
   */
  async getMessageCount(): Promise<MessengerChartData[]> {
    const response = await fetch(`${this.baseUrl}/messenger/count`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch message count: ${response.statusText}`)
    }

    const result: MessengerCountResponse = await response.json()
    
    if (!result.success) {
      throw new Error('Failed to fetch message count')
    }

    return result.data
  }

  /**
   * Get list of failure receivers
   */
  async getFailureReceivers(): Promise<MessengerReceiver[]> {
    const response = await fetch(`${this.baseUrl}/messenger/list-failure-receivers`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch failure receivers: ${response.statusText}`)
    }

    const result: MessengerApiResponse<MessengerReceiver> = await response.json()
    
    if (!result.success) {
      throw new Error('Failed to fetch failure receivers')
    }

    return result.data
  }

  /**
   * Get list of receivers
   */
  async getReceivers(): Promise<MessengerReceiver[]> {
    const response = await fetch(`${this.baseUrl}/messenger/list-receivers`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch receivers: ${response.statusText}`)
    }

    const result: MessengerApiResponse<MessengerReceiver> = await response.json()
    
    if (!result.success) {
      throw new Error('Failed to fetch receivers')
    }

    return result.data
  }

  /**
   * Get failed messages for a specific receiver
   */
  async getFailedMessages(receiverName: string): Promise<MessengerFailedMessage[]> {
    const response = await fetch(`${this.baseUrl}/messenger/list-failed/${encodeURIComponent(receiverName)}`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch failed messages: ${response.statusText}`)
    }

    const result: MessengerApiResponse<MessengerFailedMessage> = await response.json()
    
    if (!result.success) {
      throw new Error('Failed to fetch failed messages')
    }

    return result.data
  }

  /**
   * Get pending messages for a specific receiver
   */
  async getMessages(receiverName: string): Promise<MessengerMessage[]> {
    const response = await fetch(`${this.baseUrl}/messenger/list/${encodeURIComponent(receiverName)}`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch messages: ${response.statusText}`)
    }

    const result: MessengerApiResponse<MessengerMessage> = await response.json()
    
    if (!result.success) {
      throw new Error('Failed to fetch messages')
    }

    return result.data
  }

  /**
   * Delete a failed message
   */
  async deleteFailedMessage(receiverName: string, messageId: string): Promise<void> {
    const response = await fetch(`${this.baseUrl}/messenger/delete/${encodeURIComponent(receiverName)}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        id: messageId,
      }),
    })
    
    if (!response.ok) {
      throw new Error(`Failed to delete message: ${response.statusText}`)
    }

    const result: MessengerDeleteResponse = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || 'Failed to delete message')
    }
  }

  /**
   * Retry a failed message
   */
  async retryFailedMessage(receiverName: string, messageId: string): Promise<void> {
    const response = await fetch(`${this.baseUrl}/messenger/retry/${encodeURIComponent(receiverName)}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        id: messageId,
      }),
    })
    
    if (!response.ok) {
      throw new Error(`Failed to retry message: ${response.statusText}`)
    }

    const result: MessengerRetryResponse = await response.json()
    
    if (!result.success) {
      throw new Error(result.message || 'Failed to retry message')
    }
  }
}

export const messengerService = new Messenger()