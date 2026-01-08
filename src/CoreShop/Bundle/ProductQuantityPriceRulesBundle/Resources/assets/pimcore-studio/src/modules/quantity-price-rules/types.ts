/**
 * CoreShop ProductQuantityPriceRulesBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { RuleCondition } from '@coreshop/rule/src/rules/types'

/**
 * Pricing behaviour types
 */
export type PricingBehaviour = 'fixed' | 'percentage_decrease' | 'percentage_increase' | 'amount_decrease' | 'amount_increase'

/**
 * Calculation behaviour types
 */
export type CalculationBehaviour = 'by_quantity' | 'by_percentage' | 'by_price'

/**
 * A single quantity range
 */
export interface QuantityRange {
  id?: number | null
  rangeStartingFrom: number
  pricingBehaviour: PricingBehaviour
  amount?: number
  percentage?: number
  currency?: number | null
  highlighted?: boolean
}

/**
 * A quantity price rule
 */
export interface QuantityPriceRule {
  id?: number | null
  name: string
  calculationBehaviour: CalculationBehaviour
  priority: number
  active: boolean
  conditions: RuleCondition[]
  ranges: QuantityRange[]
}

/**
 * Store data configuration from backend
 */
export interface QuantityPriceRuleStoreData {
  calculationBehaviourTypes: Array<[string, string]>
  pricingBehaviourTypes: Array<[string, string]>
}

/**
 * Full data for the quantity price rules field
 */
export interface QuantityPriceRulesFieldData {
  rules: QuantityPriceRule[]
  stores: QuantityPriceRuleStoreData
  conditions: Array<{ type: string }>
  actions: Array<{ type: string }>
}
