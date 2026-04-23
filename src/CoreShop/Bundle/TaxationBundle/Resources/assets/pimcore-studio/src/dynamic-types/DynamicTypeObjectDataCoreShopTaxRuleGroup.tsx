/**
 * CoreShop TaxationBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { DynamicTypeObjectDataCoreShopSelect } from '@coreshop/resource/src/dynamic-types/DynamicTypeObjectDataCoreShopSelect'
import { loadTaxRuleGroups, getTaxRuleGroupCache, clearTaxRuleGroupCache } from '../components/TaxRuleGroupSelect'

export { loadTaxRuleGroups, getTaxRuleGroupCache, clearTaxRuleGroupCache }

export class DynamicTypeObjectDataCoreShopTaxRuleGroup extends DynamicTypeObjectDataCoreShopSelect {
  readonly id = 'coreShopTaxRuleGroup'
  loadOptions = loadTaxRuleGroups
  getCachedOptions = getTaxRuleGroupCache
}
