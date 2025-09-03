/**
 * Custom React hook for managing countries data
 * 
 * Provides reactive access to countries data with loading states and zone filtering
 */

import { useState, useEffect } from 'react'
import { ResourceService } from '@/services/ResourceService'
import { Country } from '@/types'

interface UseCountriesOptions {
  zoneId?: number
}

interface UseCountriesResult {
  countries: Country[]
  loading: boolean
  error: string | null
  refetch: () => Promise<void>
}

export const useCountries = (options: UseCountriesOptions = {}): UseCountriesResult => {
  const [countries, setCountries] = useState<Country[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  
  const resourceService = new ResourceService()

  const fetchCountries = async () => {
    setLoading(true)
    setError(null)
    
    try {
      const endpoint = options.zoneId 
        ? `zone/${options.zoneId}/countries`
        : 'country/list'
        
      const response = await resourceService.getList<Country>(endpoint)
      if (response.success) {
        setCountries(response.data)
      } else {
        setError('Failed to load countries')
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchCountries()
  }, [options.zoneId])

  return {
    countries,
    loading,
    error,
    refetch: fetchCountries
  }
}