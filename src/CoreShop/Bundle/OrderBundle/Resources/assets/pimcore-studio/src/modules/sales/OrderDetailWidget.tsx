/**
 * CoreShop OrderBundle - Order Detail Widget
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { renderApiError } from '@coreshop/resource/src/entities'
import { SaleDetail } from './SaleDetail'
import { SaleDetailSkeleton } from './SaleDetailSkeleton'
import type { Sale } from './types'

interface OrderDetailWidgetProps {
    orderId: number
}

/**
 * Loads order details from the backend API
 */
const loadOrderFromBackend = async (id: number): Promise<Sale | null> => {
  try {
    const response = await fetch(`/pimcore-studio/api/coreshop/order/detail?id=${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()

    if (data.success && data.sale) {
      return data.sale as Sale
    }

    return null
  } catch (error) {
    console.error('Error loading order:', error)
    throw error
  }
}

/**
 * Standalone Order Detail Widget
 *
 * This widget displays order details in its own tab,
 * not integrated with Pimcore's DataObject editor.
 */
export const OrderDetailWidget: React.FC<OrderDetailWidgetProps> = (config) => {
  const [order, setOrder] = React.useState<Sale | undefined>()
  const [loading, setLoading] = React.useState(true)
  const messageApi = useMessage()

  const orderId = config?.orderId

  // Load order data from backend
  // Note: messageApi is intentionally excluded from deps - it should be stable but
  // some implementations return a new reference on each render, causing infinite loops
  const loadOrder = React.useCallback(async () => {
    if (!orderId) {
      setLoading(false)
      return
    }

    try {
      setLoading(true)
      const data = await loadOrderFromBackend(orderId)

      if (data) {
        setOrder(data)
      } else {
        void messageApi.error(renderApiError('Failed to load order data'))
      }
    } catch (error) {
      void messageApi.error(renderApiError('Error loading order'))
    } finally {
      setLoading(false)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [orderId])

  // Load on mount and when orderId changes
  React.useEffect(() => {
    void loadOrder()
  }, [loadOrder])

  const handleChange = React.useCallback(async (updates: Partial<Sale>) => {
    // Optimistic update using functional updater
    setOrder(prev => prev ? { ...prev, ...updates } : prev)

    try {
      // Save to backend
      const response = await fetch(`/pimcore-studio/api/coreshop/order/update/${orderId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(updates)
      })

      if (!response.ok) {
        throw new Error('Failed to save')
      }

      void messageApi.success('Order updated successfully')

      // Reload to get fresh data
      await loadOrder()
    } catch (error) {
      void messageApi.error(renderApiError('Failed to save changes'))
      // Revert on error
      await loadOrder()
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [orderId, loadOrder])

  if (loading) {
    return <SaleDetailSkeleton />
  }

  if (!order) {
    return (
      <div style={{
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        height: '100%',
        width: '100%',
        minHeight: 400,
        flexDirection: 'column',
        gap: 16
      }}>
        <h2>Order not found</h2>
        <p style={{ color: '#999' }}>Order #{orderId} could not be loaded</p>
      </div>
    )
  }

  return (
    <SaleDetail
      sale={order}
      type="order"
      onChange={handleChange}
      onReload={loadOrder}
    />
  )
}
