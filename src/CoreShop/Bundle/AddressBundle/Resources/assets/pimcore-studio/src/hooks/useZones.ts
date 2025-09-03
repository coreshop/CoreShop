/**
 * Custom React hook for managing zones data
 * 
 * Provides reactive access to zones data with loading states
 */

import { useState, useEffect } from 'react'
import { ResourceService } from '@/services/ResourceService'
import { Zone } from '@/types'

interface UseZonesResult {
  zones: Zone[]
  loading: boolean
  error: string | null
  refetch: () => Promise<void>
}

export const useZones = (): UseZonesResult => {
  const [zones, setZones] = useState<Zone[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  
  const resourceService = new ResourceService()

  const fetchZones = async () => {
    setLoading(true)
    setError(null)
    
    try {
      const response = await resourceService.getList<Zone>('zone/list')
      if (response.success) {
        setZones(response.data)
      } else {
        setError('Failed to load zones')
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchZones()
  }, [])

  return {
    zones,
    loading,
    error,
    refetch: fetchZones
  }
}