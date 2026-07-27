/**
 * CoreShop VariantBundle API
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export interface AttributeNode {
  text: string
  id?: number
  group_id?: number
  sorting: number
  leaf: boolean
  checked?: boolean
  iconCls: string
  data?: AttributeNode[]
}

export interface AttributesResponse {
  success: boolean
  data: AttributeNode[]
}

export interface GenerateResponse {
  success: boolean
  message: string
}

const basePath = '/pimcore-studio/api/coreshop'

export const variantGeneratorApi = {
  async getAttributes(objectId: number): Promise<AttributesResponse> {
    const url = `${basePath}/variant/attributes?id=${objectId}`
    const res = await fetch(url, { credentials: 'same-origin' })
    if (!res.ok) throw new Error(`Failed to load attributes: ${res.status}`)
    return await res.json() as AttributesResponse
  },

  async generateVariants(objectId: number, attributes: Record<number, number[]>): Promise<GenerateResponse> {
    const url = `${basePath}/variant/generate`
    const res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: objectId, attributes })
    })
    if (!res.ok) throw new Error(`Failed to generate variants: ${res.status}`)
    return await res.json() as GenerateResponse
  }
}
