import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { countryApi } from '../modules/countries/api'

const countryLoader = createOptionsLoader(async () => {
  const rows = await countryApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? r.isoCode ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export const loadCountries = countryLoader.load
export const clearCountryCache = countryLoader.clearCache
export const getCountryCache = countryLoader.getCache
