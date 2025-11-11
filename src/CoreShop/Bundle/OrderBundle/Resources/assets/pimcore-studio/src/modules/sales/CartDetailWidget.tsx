/**
 * CoreShop OrderBundle - Cart Detail Widget
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

interface CartDetailWidgetProps {
  config: {
    id: number
  }
}

const loadCartFromBackend = async (id: number): Promise<Sale | null> => {
  try {
    const response = await fetch(`/admin/coreshop/order/detail?id=${id}&saleType=cart`, {
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
    console.error('Error loading cart:', error)
    throw error
  }
}

export const CartDetailWidget: React.FC<CartDetailWidgetProps> = ({ config }) => {
  const [cart, setCart] = React.useState<Sale | undefined>()
  const [loading, setLoading] = React.useState(true)

  const cartId = config?.id

  const loadCart = React.useCallback(async () => {
    if (!cartId) {
      setLoading(false)
      return
    }

    try {
      setLoading(true)
      const data = await loadCartFromBackend(cartId)

      if (data) {
        setCart(data)
      } else {
        void message.error('Failed to load cart data')
      }
    } catch (error) {
      void message.error('Error loading cart')
      console.error('Failed to load cart:', error)
    } finally {
      setLoading(false)
    }
  }, [cartId])

  React.useEffect(() => {
    void loadCart()
  }, [loadCart])

  const handleChange = async (updates: Partial<Sale>) => {
    if (!cart) return

    try {
      setCart({ ...cart, ...updates })

      const response = await fetch(`/admin/coreshop/order/update/${cartId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(updates)
      })

      if (!response.ok) {
        throw new Error('Failed to save')
      }

      void message.success('Cart updated successfully')
      await loadCart()
    } catch (error) {
      void message.error('Failed to save changes')
      await loadCart()
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
        <Spin size="large" tip="Loading cart details..." />
      </div>
    )
  }

  if (!cart) {
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
        <h2>Cart not found</h2>
        <p style={{ color: '#999' }}>Cart #{cartId} could not be loaded</p>
      </div>
    )
  }

  return (
    <SaleDetail
      sale={cart}
      type="cart"
      onChange={handleChange}
      onReload={loadCart}
    />
  )
}
