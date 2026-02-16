import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import type { SelectOption } from '@coreshop/resource/src/utils/createOptionsLoader'
import { loadCurrencies, getCurrencyCache, clearCurrencyCache } from '../components/CurrencySelect'

export { loadCurrencies, clearCurrencyCache }
export type { SelectOption as Option }

export class DynamicTypeObjectDataCoreShopCurrency extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopCurrency'
  loadOptions = loadCurrencies
  getCachedOptions = getCurrencyCache
}
