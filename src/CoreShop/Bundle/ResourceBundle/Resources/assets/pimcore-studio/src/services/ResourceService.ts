/**
 * CoreShop Resource Service
 * 
 * Handles API communication for resource management
 * This replaces the ExtJS Ajax requests with modern fetch-based API calls
 */

import { CoreShopResource, ResourceListResponse, ResourceResponse } from '@/types'

export class ResourceService {
  private baseUrl = '/admin/coreshop'

  /**
   * Fetch a list of resources
   */
  async getList<T = CoreShopResource>(endpoint: string): Promise<ResourceListResponse<T>> {
    const response = await fetch(`${this.baseUrl}/${endpoint}`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch ${endpoint}: ${response.statusText}`)
    }

    return response.json()
  }

  /**
   * Fetch a single resource by ID
   */
  async getItem<T = CoreShopResource>(endpoint: string, id: number): Promise<ResourceResponse<T>> {
    const response = await fetch(`${this.baseUrl}/${endpoint}/${id}`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch ${endpoint}/${id}: ${response.statusText}`)
    }

    return response.json()
  }

  /**
   * Create a new resource
   */
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

  /**
   * Update an existing resource
   */
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

  /**
   * Delete a resource
   */
  async delete(endpoint: string, id: number): Promise<{ success: boolean }> {
    const response = await fetch(`${this.baseUrl}/${endpoint}/${id}`, {
      method: 'DELETE',
    })
    
    if (!response.ok) {
      throw new Error(`Failed to delete ${endpoint}/${id}: ${response.statusText}`)
    }

    return { success: true }
  }

  /**
   * Get the class map (equivalent to the original coreshop_resource_class_map route)
   */
  async getClassMap(): Promise<{
    classMap: Record<string, string>
    stack: string[]
    full_stack: string[]
  }> {
    const response = await fetch(`${this.baseUrl}/resource/config`)
    
    if (!response.ok) {
      throw new Error(`Failed to fetch class map: ${response.statusText}`)
    }

    return response.json()
  }
}