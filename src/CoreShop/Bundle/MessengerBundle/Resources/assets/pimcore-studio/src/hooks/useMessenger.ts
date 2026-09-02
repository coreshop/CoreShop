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

import { useState, useEffect, useCallback, useRef } from 'react'
import { messengerService } from '../services/messenger'
import {
  MessengerChartData,
  MessengerMessage,
  MessengerFailedMessage,
  MessengerReceiver
} from '../types'
import { messengerEventEmitter, type MessengerUpdateEvent } from '../modules/mercure/messenger-event-emitter'

// Debounce helper to prevent excessive reloads
function useDebouncedCallback<T extends (...args: any[]) => void>(
  callback: T,
  delay: number
): T {
  const timeoutRef = useRef<ReturnType<typeof setTimeout> | null>(null)
  const callbackRef = useRef(callback)
  callbackRef.current = callback

  return useCallback((...args: Parameters<T>) => {
    if (timeoutRef.current) {
      clearTimeout(timeoutRef.current)
    }
    timeoutRef.current = setTimeout(() => {
      callbackRef.current(...args)
    }, delay)
  }, [delay]) as T
}

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

  // Debounced reload to prevent excessive API calls
  const debouncedReload = useDebouncedCallback(loadData, 500)

  useEffect(() => {
    loadData()
  }, [loadData])

  // Subscribe to Mercure updates via Pimcore's GlobalMessageBus - reload chart on any messenger event
  useEffect(() => {
    return messengerEventEmitter.subscribe((_event: MessengerUpdateEvent) => {
      console.debug('useMessengerChart: Received Mercure event, triggering reload')
      debouncedReload()
    })
  }, [debouncedReload])

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

  // Debounced reload to prevent excessive API calls
  const debouncedReload = useDebouncedCallback(loadData, 500)

  useEffect(() => {
    loadData()
  }, [loadData])

  // Subscribe to Mercure updates via Pimcore's GlobalMessageBus - reload when relevant events occur
  useEffect(() => {
    return messengerEventEmitter.subscribe((event: MessengerUpdateEvent) => {
      if (!receiverName) return
      // Reload on message handled (removed from pending) for this receiver
      if (event.type === 'message_handled' && event.receiverName === receiverName) {
        debouncedReload()
      }
    })
  }, [receiverName, debouncedReload])

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

  // Debounced reload to prevent excessive API calls
  const debouncedReload = useDebouncedCallback(loadData, 500)

  const deleteMessage = useCallback(async (messageId: string) => {
    if (!receiverName) return

    try {
      await messengerService.deleteFailedMessage(receiverName, messageId)
      // Note: Mercure will trigger reload automatically, but we also reload here
      // for immediate feedback in case Mercure is slow or unavailable
      await loadData()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to delete message')
      throw err
    }
  }, [receiverName, loadData])

  const retryMessage = useCallback(async (messageId: string) => {
    if (!receiverName) return

    try {
      await messengerService.retryFailedMessage(receiverName, messageId)
      // Note: Mercure will trigger reload automatically, but we also reload here
      // for immediate feedback in case Mercure is slow or unavailable
      await loadData()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to retry message')
      throw err
    }
  }, [receiverName, loadData])

  useEffect(() => {
    loadData()
  }, [loadData])

  // Subscribe to Mercure updates via Pimcore's GlobalMessageBus - reload when relevant events occur
  useEffect(() => {
    return messengerEventEmitter.subscribe((event: MessengerUpdateEvent) => {
      if (!receiverName) return
      // Reload on failed messages for this receiver
      if (event.type === 'message_failed' && event.receiverName === receiverName) {
        debouncedReload()
      }
      // Also reload on retry/reject as these come from other users or tabs
      if ((event.type === 'message_retried' || event.type === 'message_rejected') &&
          event.receiverName === receiverName) {
        debouncedReload()
      }
    })
  }, [receiverName, debouncedReload])

  return {
    messages,
    loading,
    error,
    reload: loadData,
    deleteMessage,
    retryMessage
  }
}