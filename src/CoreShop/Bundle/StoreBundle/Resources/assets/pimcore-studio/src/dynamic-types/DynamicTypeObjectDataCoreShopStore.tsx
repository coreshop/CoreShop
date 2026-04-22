import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { loadStores, getStoreCache } from '../components/StoreSelect'

export class DynamicTypeObjectDataCoreShopStore extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopStore'
  loadOptions = loadStores
  getCachedOptions = getStoreCache
}
