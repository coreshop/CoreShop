/**
 * CoreShop ProductBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { Rule } from '@coreshop/rule/src/rules'

export interface ProductSpecificPriceRuleTranslation {
  label?: string
}

export interface ProductSpecificPriceRule extends Rule {
  id?: number
  product?: number
  inherit?: boolean
  translations?: Record<string, ProductSpecificPriceRuleTranslation>
}

export interface ProductSpecificPriceRulesData {
  actions: string[]
  conditions: string[]
  actionSchemaByType?: Record<string, string>
  conditionSchemaByType?: Record<string, string>
  rules: ProductSpecificPriceRule[]
}
