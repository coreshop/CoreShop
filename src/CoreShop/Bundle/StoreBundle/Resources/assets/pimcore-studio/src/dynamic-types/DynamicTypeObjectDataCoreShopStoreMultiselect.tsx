import { DynamicTypeObjectDataCoreShopMultiSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopMultiSelect'
import { loadStores, getStoreCache } from '../components/StoreSelect'

export { clearStoreCache } from '../components/StoreSelect'

export class DynamicTypeObjectDataCoreShopStoreMultiselect extends DynamicTypeObjectDataCoreShopMultiSelect {
  readonly id = 'coreShopStoreMultiselect'
  loadOptions = loadStores
  getCachedOptions = getStoreCache
}
