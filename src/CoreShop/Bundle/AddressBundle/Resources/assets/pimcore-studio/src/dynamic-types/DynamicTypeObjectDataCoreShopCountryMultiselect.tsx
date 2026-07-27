import { DynamicTypeObjectDataCoreShopMultiSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopMultiSelect'
import { loadCountries, getCountryCache } from '../components/CountrySelect'

export class DynamicTypeObjectDataCoreShopCountryMultiselect extends DynamicTypeObjectDataCoreShopMultiSelect {
  readonly id = 'coreShopCountryMultiselect'
  loadOptions = loadCountries
  getCachedOptions = getCountryCache
}
