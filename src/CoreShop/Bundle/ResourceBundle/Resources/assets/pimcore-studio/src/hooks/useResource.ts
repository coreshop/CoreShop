/**
 * Custom React hook for managing generic resource data
 * 
 * Provides reactive access to any CoreShop resource with CRUD operations
 */

import { useState, useCallback } from 'react'
import { ResourceService } from '@/services/ResourceService'
import { CoreShopResource, ResourceListResponse, ResourceResponse } from '@/types'

interface UseResourceOptions {
  endpoint: string
  autoLoad?: boolean
}

interface UseResourceResult<T = CoreShopResource> {
  data: T[]
  loading: boolean
  error: string | null
  loadData: () => Promise<void>
  createItem: (item: Partial<T>) => Promise<T | null>
  updateItem: (id: number, item: Partial<T>) => Promise<T | null>
  deleteItem: (id: number) => Promise<boolean>
  getItem: (id: number) => Promise<T | null>
}

export const useResource = <T = CoreShopResource>({
  endpoint,
  autoLoad = true
}: UseResourceOptions): UseResourceResult<T> => {
  const [data, setData] = useState<T[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  
  const resourceService = new ResourceService()

  const loadData = useCallback(async () => {
    setLoading(true)
    setError(null)
    
    try {
      const response = await resourceService.getList<T>(endpoint)
      if (response.success) {
        setData(response.data)
      } else {
        setError('Failed to load data')
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
    } finally {
      setLoading(false)
    }
  }, [endpoint])

  const createItem = useCallback(async (item: Partial<T>): Promise<T | null> => {
    try {
      const response = await resourceService.create<T>(endpoint, item)
      if (response.success) {
        await loadData() // Refresh the list
        return response.data
      } else {
        setError('Failed to create item')
        return null
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
      return null
    }
  }, [endpoint, loadData])

  const updateItem = useCallback(async (id: number, item: Partial<T>): Promise<T | null> => {
    try {
      const response = await resourceService.update<T>(endpoint, id, item)
      if (response.success) {
        await loadData() // Refresh the list
        return response.data
      } else {
        setError('Failed to update item')
        return null
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
      return null
    }
  }, [endpoint, loadData])

  const deleteItem = useCallback(async (id: number): Promise<boolean> => {
    try {
      await resourceService.delete(endpoint, id)
      await loadData() // Refresh the list
      return true
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
      return false
    }
  }, [endpoint, loadData])

  const getItem = useCallback(async (id: number): Promise<T | null> => {
    try {
      const response = await resourceService.getItem<T>(endpoint, id)
      if (response.success) {
        return response.data
      } else {
        setError('Failed to get item')
        return null
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
      return null
    }
  }, [endpoint])

  // Auto-load data on mount if enabled
  React.useEffect(() => {
    if (autoLoad) {
      loadData()
    }
  }, [loadData, autoLoad])

  return {
    data,
    loading,
    error,
    loadData,
    createItem,
    updateItem,
    deleteItem,
    getItem
  }
}

// Re-export React for the useEffect hook
import React from 'react'