import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { addressIdentifierApi } from '../modules/address-identifiers/api'

const { load: loadAddressIdentifiers, getCache, clearCache: clearAddressIdentifierCache } = createOptionsLoader(async () => {
  const rows = await addressIdentifierApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export { loadAddressIdentifiers, clearAddressIdentifierCache }

export class DynamicTypeObjectDataCoreShopAddressIdentifier extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopAddressIdentifier'
  loadOptions = loadAddressIdentifiers
  getCachedOptions = getCache
}
