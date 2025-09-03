/**
 * Custom React hook for managing states data
 * 
 * Provides reactive access to states data with country filtering
 */

import { useState, useEffect } from 'react'
import { ResourceService } from '@/services/ResourceService'
import { State } from '@/types'

interface UseStatesOptions {
  countryId?: number
}

interface UseStatesResult {
  states: State[]
  loading: boolean
  error: string | null
  refetch: () => Promise<void>
}

export const useStates = (options: UseStatesOptions = {}): UseStatesResult => {
  const [states, setStates] = useState<State[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  
  const resourceService = new ResourceService()

  const fetchStates = async () => {
    setLoading(true)
    setError(null)
    
    try {
      const endpoint = options.countryId 
        ? `country/${options.countryId}/states`
        : 'state/list'
        
      const response = await resourceService.getList<State>(endpoint)
      if (response.success) {
        setStates(response.data)
      } else {
        setError('Failed to load states')
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchStates()
  }, [options.countryId])

  return {
    states,
    loading,
    error,
    refetch: fetchStates
  }
}