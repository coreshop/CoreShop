import type { SelectOption } from '../entities/hooks/useEntitySelect'
export type { SelectOption }

export function createOptionsLoader(
  fetchFn: () => Promise<SelectOption[]>
): {
  load: () => Promise<SelectOption[]>
  getCache: () => SelectOption[] | null
  clearCache: () => void
} {
  let cachedOptions: SelectOption[] | null = null
  let loadPromise: Promise<SelectOption[]> | null = null

  const load = async (): Promise<SelectOption[]> => {
    if (cachedOptions) return cachedOptions
    if (loadPromise) return loadPromise

    loadPromise = (async () => {
      try {
        cachedOptions = await fetchFn()
        return cachedOptions
      } catch (err) {
        console.error('Failed to load options:', err)
        return []
      } finally {
        loadPromise = null
      }
    })()

    return loadPromise
  }

  const getCache = (): SelectOption[] | null => cachedOptions

  const clearCache = (): void => {
    cachedOptions = null
    loadPromise = null
  }

  return { load, getCache, clearCache }
}
