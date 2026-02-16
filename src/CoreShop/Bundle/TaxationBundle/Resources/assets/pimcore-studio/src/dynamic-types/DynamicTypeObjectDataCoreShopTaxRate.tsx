/**
 * CoreShop TaxationBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { taxRateApi } from '../modules/tax-rates/api'

const { load: loadTaxRates, getCache: getTaxRateCache, clearCache: clearTaxRateCache } = createOptionsLoader(async () => {
  const rows = await taxRateApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export { loadTaxRates, getTaxRateCache, clearTaxRateCache }

export class DynamicTypeObjectDataCoreShopTaxRate extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopTaxRate'
  loadOptions = loadTaxRates
  getCachedOptions = getTaxRateCache
}
