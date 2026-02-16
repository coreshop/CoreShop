import { DynamicTypeObjectDataCoreShopMultiSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopMultiSelect'
import { loadCarriers, getCarrierCache } from '../components/CarrierSelect'

export class DynamicTypeObjectDataCoreShopCarrierMultiselect extends DynamicTypeObjectDataCoreShopMultiSelect {
  readonly id = 'coreShopCarrierMultiselect'
  loadOptions = loadCarriers
  getCachedOptions = getCarrierCache
}
