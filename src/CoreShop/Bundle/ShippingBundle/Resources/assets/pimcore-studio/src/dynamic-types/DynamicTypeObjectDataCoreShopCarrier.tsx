import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { loadCarriers, getCarrierCache, clearCarrierCache } from '../components/CarrierSelect'

export { loadCarriers, clearCarrierCache }

export class DynamicTypeObjectDataCoreShopCarrier extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopCarrier'
  loadOptions = loadCarriers
  getCachedOptions = getCarrierCache
}
