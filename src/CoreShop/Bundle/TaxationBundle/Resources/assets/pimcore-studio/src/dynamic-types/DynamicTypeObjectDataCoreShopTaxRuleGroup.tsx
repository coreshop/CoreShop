/**
 * CoreShop TaxationBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { createOptionsLoader } from '@coreshop/resource/src/utils/createOptionsLoader'
import { taxRuleGroupApi } from '../modules/tax-rule-groups/api'

const { load: loadTaxRuleGroups, getCache: getTaxRuleGroupCache, clearCache: clearTaxRuleGroupCache } = createOptionsLoader(async () => {
  const rows = await taxRuleGroupApi.list()
  return (Array.isArray(rows) ? rows : [])
    .map((r: any) => ({ value: r.id, label: r.name ?? String(r.id) }))
    .filter((o: any) => o.value != null && o.label)
})

export { loadTaxRuleGroups, getTaxRuleGroupCache, clearTaxRuleGroupCache }

export class DynamicTypeObjectDataCoreShopTaxRuleGroup extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopTaxRuleGroup'
  loadOptions = loadTaxRuleGroups
  getCachedOptions = getTaxRuleGroupCache
}
