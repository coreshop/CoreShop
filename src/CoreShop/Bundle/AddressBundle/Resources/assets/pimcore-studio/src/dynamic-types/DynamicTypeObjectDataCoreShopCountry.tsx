import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { loadCountries, clearCountryCache, getCountryCache } from '../components/CountrySelect'

export { clearCountryCache }

export class DynamicTypeObjectDataCoreShopCountry extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopCountry'
  loadOptions = loadCountries
  getCachedOptions = getCountryCache
}
