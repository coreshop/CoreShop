import { DynamicTypeObjectDataCoreShopMultiSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopMultiSelect'
import { loadStores, getStoreCache, clearStoreCache } from '../components/StoreSelect'

export { clearStoreCache }

export class DynamicTypeObjectDataCoreShopStoreMultiselect extends DynamicTypeObjectDataCoreShopMultiSelect {
  readonly id = 'coreShopStoreMultiselect'
  loadOptions = loadStores
  getCachedOptions = getStoreCache
}
