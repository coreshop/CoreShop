import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { currencyApi } from '../modules/currencies/api'

const { load: loadCurrencies, getCache: getCurrencyCache, clearCache: clearCurrencyCache } = createOptionsLoader(async () => {
  const rows = await currencyApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? r.isoCode ?? r.code ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export { loadCurrencies, getCurrencyCache, clearCurrencyCache }
