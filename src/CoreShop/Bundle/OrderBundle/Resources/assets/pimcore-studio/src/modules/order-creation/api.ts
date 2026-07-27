/**
 * CoreShop OrderBundle - Order Creation API
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type {
  CustomerDetails,
  OrderCreationPreview,
  OrderCreationRequest,
  OrderCreationItem
} from './types'

interface ApiResponse<T> {
  success: boolean
  data?: T
  customer?: CustomerDetails
  id?: number
  message?: string | Record<string, string[]>
}

/**
 * Convert request to URLSearchParams, handling nested items array
 */
function buildUrlSearchParams(request: Record<string, unknown>): URLSearchParams {
  const params = new URLSearchParams()

  for (const [key, value] of Object.entries(request)) {
    if (key === 'items' && Array.isArray(value)) {
      // Handle items array in form-style format
      const items = value as OrderCreationItem[]
      items.forEach((item, index) => {
        params.append(`items[${index}][product]`, String(item.product))
        params.append(`items[${index}][quantity]`, String(item.quantity))
        if (item.customItemPrice !== undefined) {
          params.append(`items[${index}][customItemPrice]`, String(item.customItemPrice))
        }
        if (item.customItemDiscount !== undefined) {
          params.append(`items[${index}][customItemDiscount]`, String(item.customItemDiscount))
        }
        if (item.unitDefinition !== undefined) {
          params.append(`items[${index}][unitDefinition]`, String(item.unitDefinition))
        }
      })
    } else if (value !== null && value !== undefined) {
      params.append(key, String(value))
    }
  }

  return params
}

class OrderCreationApi {
  private basePath = '/pimcore-studio/api/coreshop/order-creation'

  /**
   * Get customer details by ID
   */
  async getCustomerDetails(customerId: number): Promise<CustomerDetails> {
    const url = `${this.basePath}/get-customer-details?customerId=${customerId}`
    const res = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin'
    })

    if (!res.ok) {
      throw new Error('Failed to fetch customer details')
    }

    const data: ApiResponse<CustomerDetails> = await res.json()
    if (!data.success) {
      throw new Error(
        typeof data.message === 'string' ? data.message : 'Failed to fetch customer'
      )
    }

    if (!data.customer) {
      throw new Error('Customer not found in response')
    }

    return data.customer
  }

  /**
   * Preview order/cart with current data
   */
  async preview(request: Record<string, unknown>): Promise<OrderCreationPreview> {
    const params = buildUrlSearchParams(request)

    const res = await fetch(`${this.basePath}/preview`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      credentials: 'same-origin',
      body: params
    })

    if (!res.ok) {
      throw new Error('Preview request failed')
    }

    const data: ApiResponse<OrderCreationPreview> = await res.json()
    if (!data.success) {
      const message =
        typeof data.message === 'string'
          ? data.message
          : data.message
            ? JSON.stringify(data.message)
            : 'Preview failed'
      throw new Error(message)
    }

    if (!data.data) {
      throw new Error('Preview data not found in response')
    }

    return data.data
  }

  /**
   * Create cart or order
   */
  async create(request: OrderCreationRequest): Promise<{ success: boolean; id: number }> {
    const params = buildUrlSearchParams(request as unknown as Record<string, unknown>)

    const res = await fetch(`${this.basePath}/create`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      credentials: 'same-origin',
      body: params
    })

    if (!res.ok) {
      throw new Error('Create request failed')
    }

    const data: ApiResponse<never> = await res.json()
    if (!data.success) {
      const message =
        typeof data.message === 'string'
          ? data.message
          : data.message
            ? JSON.stringify(data.message)
            : 'Create failed'
      throw new Error(message)
    }

    if (data.id === undefined) {
      throw new Error('Created ID not found in response')
    }

    return { success: true, id: data.id }
  }
}

export const orderCreationApi = new OrderCreationApi()
