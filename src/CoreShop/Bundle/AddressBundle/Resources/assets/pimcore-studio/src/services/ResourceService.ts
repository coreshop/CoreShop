/**
 * CoreShop Resource Service (AddressBundle copy)
 * 
 * Handles API communication for address resource management
 */

export interface CoreShopResource {
  id: number
  name: string
  [key: string]: any
}

export interface ResourceListResponse<T = CoreShopResource> {
  success: boolean
  data: T[]
  total?: number
}

export interface ResourceResponse<T = CoreShopResource> {
  success: boolean
  data: T
  message?: string
}

export class ResourceService {
  private baseUrl = '/admin/api/coreshop'

  async getList<T = CoreShopResource>(endpoint: string): Promise<ResourceListResponse<T>> {
    const response = await fetch(`${this.baseUrl}/${endpoint}`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch ${endpoint}: ${response.statusText}`)
    }

    return response.json()
  }

  async getItem<T = CoreShopResource>(endpoint: string, id: number): Promise<ResourceResponse<T>> {
    const response = await fetch(`${this.baseUrl}/${endpoint}/${id}`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch ${endpoint}/${id}: ${response.statusText}`)
    }

    return response.json()
  }

  async create<T = CoreShopResource>(endpoint: string, data: Partial<T>): Promise<ResourceResponse<T>> {
    const response = await fetch(`${this.baseUrl}/${endpoint}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    
    if (!response.ok) {
      throw new Error(`Failed to create ${endpoint}: ${response.statusText}`)
    }

    return response.json()
  }

  async update<T = CoreShopResource>(endpoint: string, id: number, data: Partial<T>): Promise<ResourceResponse<T>> {
    const response = await fetch(`${this.baseUrl}/${endpoint}/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    
    if (!response.ok) {
      throw new Error(`Failed to update ${endpoint}/${id}: ${response.statusText}`)
    }

    return response.json()
  }

  async delete(endpoint: string, id: number): Promise<{ success: boolean }> {
    const response = await fetch(`${this.baseUrl}/${endpoint}/${id}`, {
      method: 'DELETE',
    })
    
    if (!response.ok) {
      throw new Error(`Failed to delete ${endpoint}/${id}: ${response.statusText}`)
    }

    return { success: true }
  }
}