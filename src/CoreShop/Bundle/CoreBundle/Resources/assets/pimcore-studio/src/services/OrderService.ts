/**
 * CoreShop Order Service
 * 
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

interface OrderFindResponse {
  success: boolean
  id?: number
  message?: string
}

export class OrderService {
  private baseUrl = '/pimcore-studio/api/coreshop'

  /**
   * Find order by number or Pimcore object ID
   */
  async findOrder(searchValue: string): Promise<OrderFindResponse> {
    const response = await fetch(`${this.baseUrl}/order/find`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ number: searchValue })
    })
    
    if (!response.ok) {
      throw new Error(`Failed to search for order: ${response.statusText}`)
    }

    return await response.json()
  }

}

export const orderService = new OrderService()