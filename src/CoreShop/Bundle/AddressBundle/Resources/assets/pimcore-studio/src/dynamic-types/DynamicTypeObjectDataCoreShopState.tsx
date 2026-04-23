import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { loadStates, getStateCache, clearStateCache } from '../components/StateSelect'

export { loadStates, clearStateCache }

export class DynamicTypeObjectDataCoreShopState extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopState'
  loadOptions = loadStates
  getCachedOptions = getStateCache
}
