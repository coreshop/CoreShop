/**
 * CoreShop TaxationBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { EntityApi } from '@coreshop/resource/src/entities'

export interface TaxRule {
  id?: number
  taxRuleGroup: number
  taxRate: number
  behavior: number
  country?: number
  state?: number
}

export interface TaxRuleGroupDetail extends Record<string, any> {
  id: number
  name: string
  active: boolean
  taxRules: TaxRule[]
}

export const taxRuleGroupApi = new EntityApi<TaxRuleGroupDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/tax_rule_groups'
})