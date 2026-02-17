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
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { renderApiError } from '@coreshop/resource/src/entities'
import { SaleDetail } from './SaleDetail'
import { SaleDetailSkeleton } from './SaleDetailSkeleton'
import type { Sale } from './types'

interface CartDetailWidgetProps {
  orderId: number
}

const loadCartFromBackend = async (id: number): Promise<Sale | null> => {
  try {
    const response = await fetch(`/pimcore-studio/api/coreshop/order/detail?id=${id}&saleType=cart`, {
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

export const CartDetailWidget: React.FC<CartDetailWidgetProps> = (props) => {
  const [cart, setCart] = React.useState<Sale | undefined>()
  const [loading, setLoading] = React.useState(true)
  const messageApi = useMessage()

  const cartId = props?.orderId

  // Note: messageApi is intentionally excluded from deps to prevent infinite loops
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
        void messageApi.error(renderApiError('Failed to load cart data'))
      }
    } catch (error) {
      void messageApi.error(renderApiError('Error loading cart'))
    } finally {
      setLoading(false)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [cartId])

  React.useEffect(() => {
    void loadCart()
  }, [loadCart])

  const handleChange = React.useCallback(async (updates: Partial<Sale>) => {
    setCart(prev => prev ? { ...prev, ...updates } : prev)

    try {
      const response = await fetch(`/pimcore-studio/api/coreshop/order/update/${cartId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(updates)
      })

      if (!response.ok) {
        throw new Error('Failed to save')
      }

      void messageApi.success('Cart updated successfully')
      await loadCart()
    } catch (error) {
      void messageApi.error(renderApiError('Failed to save changes'))
      await loadCart()
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [cartId, loadCart])

  if (loading) {
    return <SaleDetailSkeleton />
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
