import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { stateApi } from '../modules/states/api'

const stateLoader = createOptionsLoader(async () => {
  const rows = await stateApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({
      value: r.id,
      label: r.countryName ? `${r.name} (${r.countryName})` : (r.name ?? String(r.id))
    }))
    .filter((o: any) => o.value != null && o.label)
})

export const loadStates = stateLoader.load
export const getStateCache = stateLoader.getCache
export const clearStateCache = stateLoader.clearCache
