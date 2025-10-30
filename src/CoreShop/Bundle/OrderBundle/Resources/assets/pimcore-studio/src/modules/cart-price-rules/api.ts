/**
 * CoreShop OrderBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { RuleApi } from '@coreshop/rule/src/rules'
import type { CartPriceRule, VoucherCode, VoucherCodeGenerateParams } from './types'

export class CartPriceRuleApi extends RuleApi<CartPriceRule> {
  /**
   * Get voucher codes for a cart price rule
   */
  async getVoucherCodes(cartPriceRuleId: number, params?: { start?: number; limit?: number }): Promise<{ data: VoucherCode[]; total: number }> {
    const cfg = (this as any).cfg
    const queryParams = new URLSearchParams()
    queryParams.append('cartPriceRule', cartPriceRuleId.toString())
    if (params?.start !== undefined) queryParams.append('start', params.start.toString())
    if (params?.limit !== undefined) queryParams.append('limit', params.limit.toString())

    const url = `${cfg.basePath}${cfg.resourcePath}/get-voucher-codes?${queryParams}`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`Failed to get voucher codes: ${response.statusText}`)
    }

    return response.json()
  }

  /**
   * Create a single voucher code
   */
  async createVoucherCode(cartPriceRuleId: number, code: string): Promise<VoucherCode> {
    const cfg = (this as any).cfg
    const url = `${cfg.basePath}${cfg.resourcePath}/create-voucher-code`
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        cartPriceRule: cartPriceRuleId,
        code
      })
    })

    if (!response.ok) {
      const error = await response.json()
      throw new Error(error.message || 'Failed to create voucher code')
    }

    const result = await response.json()
    if (!result.success) {
      throw new Error(result.message || 'Failed to create voucher code')
    }

    return result.data
  }

  /**
   * Generate multiple voucher codes
   */
  async generateVoucherCodes(params: VoucherCodeGenerateParams): Promise<{ success: boolean; message?: string }> {
    const cfg = (this as any).cfg
    const url = `${cfg.basePath}${cfg.resourcePath}/generate-voucher-codes`
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify(params)
    })

    if (!response.ok) {
      const error = await response.json()
      throw new Error(error.message || 'Failed to generate voucher codes')
    }

    const result = await response.json()
    if (!result.success) {
      throw new Error(result.message || 'Failed to generate voucher codes')
    }

    return result
  }

  /**
   * Delete a voucher code
   */
  async deleteVoucherCode(voucherCodeId: number): Promise<void> {
    const cfg = (this as any).cfg
    const url = `${cfg.basePath}${cfg.resourcePath}/delete-voucher-code`
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ id: voucherCodeId })
    })

    if (!response.ok) {
      throw new Error(`Failed to delete voucher code: ${response.statusText}`)
    }
  }

  /**
   * Get export URL for voucher codes
   */
  getVoucherCodesExportUrl(cartPriceRuleId: number, params?: { start?: number; limit?: number }): string {
    const cfg = (this as any).cfg
    const queryParams = new URLSearchParams()
    queryParams.append('cartPriceRule', cartPriceRuleId.toString())
    if (params?.start !== undefined) queryParams.append('start', params.start.toString())
    if (params?.limit !== undefined) queryParams.append('limit', params.limit.toString())

    return `${cfg.basePath}${cfg.resourcePath}/export-voucher-codes?${queryParams}`
  }

  /**
   * Get cart item configuration (available conditions and actions)
   */
  async getCartItemConfig(): Promise<{ conditions: string[]; actions: string[] }> {
    const cfg = (this as any).cfg
    const url = `${cfg.basePath}${cfg.resourcePath}/get-cart-item-config`
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`Failed to get cart item config: ${response.statusText}`)
    }

    return response.json()
  }
}

export const cartPriceRuleApi = new CartPriceRuleApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/cart_price_rules'
})
