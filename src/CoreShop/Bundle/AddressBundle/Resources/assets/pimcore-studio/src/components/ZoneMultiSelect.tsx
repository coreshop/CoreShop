import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { zoneApi } from '../modules/zones/api'

const zoneLoader = createOptionsLoader(async () => {
  const rows = await zoneApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export const loadZones = zoneLoader.load
export const getZoneCache = zoneLoader.getCache
export const clearZoneCache = zoneLoader.clearCache
