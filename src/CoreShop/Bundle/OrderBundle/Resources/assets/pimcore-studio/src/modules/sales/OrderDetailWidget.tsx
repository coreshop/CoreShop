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
import { message, Spin } from 'antd'
import { SaleDetail } from './SaleDetail'
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

  const orderId = config?.orderId

  // Load order data from backend
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
        void message.error('Failed to load order data')
      }
    } catch (error) {
      void message.error('Error loading order')
      console.error('Failed to load order:', error)
    } finally {
      setLoading(false)
    }
  }, [orderId])

  // Load on mount and when orderId changes
  React.useEffect(() => {
    void loadOrder()
  }, [loadOrder])

  const handleChange = async (updates: Partial<Sale>) => {
    if (!order) return

    try {
      // Optimistic update
      setOrder({ ...order, ...updates })

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

      void message.success('Order updated successfully')

      // Reload to get fresh data
      await loadOrder()
    } catch (error) {
      void message.error('Failed to save changes')
      // Revert on error
      await loadOrder()
    }
  }

  if (loading) {
    return (
      <div style={{
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        height: '100%',
        width: '100%',
        minHeight: 400
      }}>
        <Spin size="large" tip="Loading order details..." />
      </div>
    )
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
