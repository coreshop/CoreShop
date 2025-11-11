/**
 * CoreShop OrderBundle Sale API
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { EntityApi } from '@coreshop/resource'
import type { Sale, SaleType, StateList } from './types'

/**
 * Sale API for Order, Cart, and Quote management
 *
 * Uses type-based routing to handle all three sale types with a single API class.
 */
export class SaleApi extends EntityApi<Sale> {
  private readonly saleType: SaleType

  constructor(type: SaleType) {
    // Base path is /admin/order for all types
    super('/admin/order')
    this.saleType = type
  }

  /**
   * Get a single sale by ID
   */
  async get(id: number): Promise<{ data: Sale }> {
    const response = await fetch(
      `/admin/order/get-order?id=${id}&saleType=${this.saleType}`,
      {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )

    if (!response.ok) {
      throw new Error(`Failed to fetch ${this.saleType} ${id}`)
    }

    const data = await response.json()
    return { data: { ...data, type: this.saleType } as Sale }
  }

  /**
   * List all sales of this type
   */
  async list(): Promise<Sale[]> {
    const response = await fetch(
      `/admin/order/list?saleType=${this.saleType}`,
      {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )

    if (!response.ok) {
      throw new Error(`Failed to list ${this.saleType}s`)
    }

    const data = await response.json()
    return (data.data || []).map((item: any) => ({ ...item, type: this.saleType }))
  }

  /**
   * Save/update a sale
   */
  async save(payload: Partial<Sale>): Promise<{ data: Sale }> {
    const response = await fetch(
      `/admin/order/update?id=${payload.id}`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      }
    )

    if (!response.ok) {
      throw new Error(`Failed to save ${this.saleType}`)
    }

    const data = await response.json()
    return { data: { ...data, type: this.saleType } as Sale }
  }

  /**
   * Delete a sale
   */
  async delete(id: number): Promise<void> {
    const response = await fetch(
      `/admin/order/delete?id=${id}&saleType=${this.saleType}`,
      {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )

    if (!response.ok) {
      throw new Error(`Failed to delete ${this.saleType} ${id}`)
    }
  }

  /**
   * Create a new sale
   */
  async add(data: { customer?: number }): Promise<{ data: Sale }> {
    const endpoint = this.saleType === 'cart'
      ? '/admin/cart/create'
      : this.saleType === 'quote'
        ? '/admin/quote/create'
        : '/admin/order/create'

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })

    if (!response.ok) {
      throw new Error(`Failed to create ${this.saleType}`)
    }

    const result = await response.json()
    return { data: { ...result, type: this.saleType } as Sale }
  }

  /**
   * Get available states for orders
   */
  async getStates(): Promise<StateList> {
    const response = await fetch('/admin/order/get-states', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch states')
    }

    return await response.json()
  }

  /**
   * Find sale by number
   */
  async findByNumber(number: string): Promise<Sale | null> {
    const response = await fetch(
      `/admin/${this.saleType}/find?number=${encodeURIComponent(number)}`,
      {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )

    if (!response.ok) {
      return null
    }

    const data = await response.json()
    return data ? { ...data, type: this.saleType } as Sale : null
  }
}

/**
 * Factory function to create type-specific API instances
 */
export const createSaleApi = (type: SaleType): SaleApi => new SaleApi(type)

/**
 * Pre-configured API instances for each sale type
 */
export const orderApi = createSaleApi('order')
export const cartApi = createSaleApi('cart')
export const quoteApi = createSaleApi('quote')
