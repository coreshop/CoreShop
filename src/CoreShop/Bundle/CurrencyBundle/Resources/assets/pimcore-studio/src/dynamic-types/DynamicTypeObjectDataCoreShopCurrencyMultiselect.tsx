import { DynamicTypeObjectDataCoreShopMultiSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopMultiSelect'
import { loadCurrencies, getCurrencyCache } from '../components/CurrencySelect'

export class DynamicTypeObjectDataCoreShopCurrencyMultiselect extends DynamicTypeObjectDataCoreShopMultiSelect {
  readonly id = 'coreShopCurrencyMultiselect'
  loadOptions = loadCurrencies
  getCachedOptions = getCurrencyCache
}
