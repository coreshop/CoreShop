/**
 * CoreShop TaxationBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { loadTaxRates, getTaxRateCache, clearTaxRateCache } from '../components/TaxRateSelect'

export { loadTaxRates, getTaxRateCache, clearTaxRateCache }

export class DynamicTypeObjectDataCoreShopTaxRate extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopTaxRate'
  loadOptions = loadTaxRates
  getCachedOptions = getTaxRateCache
}
