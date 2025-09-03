/**
 * CoreShop MessengerBundle React Hooks
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { useState, useEffect, useCallback } from 'react'
import { messengerService } from '../services/messenger'
import {
  MessengerChartData,
  MessengerMessage,
  MessengerFailedMessage,
  MessengerReceiver
} from '../types'

export interface UseMessengerChartResult {
  data: MessengerChartData[]
  loading: boolean
  error: string | null
  reload: () => void
}

export function useMessengerChart(): UseMessengerChartResult {
  const [data, setData] = useState<MessengerChartData[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const loadData = useCallback(async () => {
    try {
      setLoading(true)
      setError(null)
      const chartData = await messengerService.getMessageCount()
      setData(chartData)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load chart data')
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    loadData()
  }, [loadData])

  return {
    data,
    loading,
    error,
    reload: loadData
  }
}

export interface UseMessengerReceiversResult {
  receivers: MessengerReceiver[]
  failureReceivers: MessengerReceiver[]
  loading: boolean
  error: string | null
  reload: () => void
}

export function useMessengerReceivers(): UseMessengerReceiversResult {
  const [receivers, setReceivers] = useState<MessengerReceiver[]>([])
  const [failureReceivers, setFailureReceivers] = useState<MessengerReceiver[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const loadData = useCallback(async () => {
    try {
      setLoading(true)
      setError(null)
      const [receiversData, failureReceiversData] = await Promise.all([
        messengerService.getReceivers(),
        messengerService.getFailureReceivers()
      ])
      setReceivers(receiversData)
      setFailureReceivers(failureReceiversData)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load receivers')
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    loadData()
  }, [loadData])

  return {
    receivers,
    failureReceivers,
    loading,
    error,
    reload: loadData
  }
}

export interface UseMessengerMessagesResult {
  messages: MessengerMessage[]
  loading: boolean
  error: string | null
  reload: () => void
}

export function useMessengerMessages(receiverName: string | null): UseMessengerMessagesResult {
  const [messages, setMessages] = useState<MessengerMessage[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const loadData = useCallback(async () => {
    if (!receiverName) {
      setMessages([])
      return
    }

    try {
      setLoading(true)
      setError(null)
      const messagesData = await messengerService.getMessages(receiverName)
      setMessages(messagesData)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load messages')
    } finally {
      setLoading(false)
    }
  }, [receiverName])

  useEffect(() => {
    loadData()
  }, [loadData])

  return {
    messages,
    loading,
    error,
    reload: loadData
  }
}

export interface UseMessengerFailedMessagesResult {
  messages: MessengerFailedMessage[]
  loading: boolean
  error: string | null
  reload: () => void
  deleteMessage: (messageId: string) => Promise<void>
  retryMessage: (messageId: string) => Promise<void>
}

export function useMessengerFailedMessages(receiverName: string | null): UseMessengerFailedMessagesResult {
  const [messages, setMessages] = useState<MessengerFailedMessage[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const loadData = useCallback(async () => {
    if (!receiverName) {
      setMessages([])
      return
    }

    try {
      setLoading(true)
      setError(null)
      const messagesData = await messengerService.getFailedMessages(receiverName)
      setMessages(messagesData)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load failed messages')
    } finally {
      setLoading(false)
    }
  }, [receiverName])

  const deleteMessage = useCallback(async (messageId: string) => {
    if (!receiverName) return

    try {
      await messengerService.deleteFailedMessage(receiverName, messageId)
      await loadData() // Reload data after deletion
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to delete message')
      throw err
    }
  }, [receiverName, loadData])

  const retryMessage = useCallback(async (messageId: string) => {
    if (!receiverName) return

    try {
      await messengerService.retryFailedMessage(receiverName, messageId)
      await loadData() // Reload data after retry
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to retry message')
      throw err
    }
  }, [receiverName, loadData])

  useEffect(() => {
    loadData()
  }, [loadData])

  return {
    messages,
    loading,
    error,
    reload: loadData,
    deleteMessage,
    retryMessage
  }
}