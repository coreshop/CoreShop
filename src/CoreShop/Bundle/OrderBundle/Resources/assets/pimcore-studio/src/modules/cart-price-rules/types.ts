/**
 * CoreShop OrderBundle Studio Plugin
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

export interface CartPriceRuleTranslation {
  label: string
}

export interface CartPriceRule extends Rule {
  isVoucherRule?: boolean
  translations?: Record<string, CartPriceRuleTranslation>
}

export interface VoucherCode {
  id: number
  code: string
  used: boolean
  uses: number
  creationDate?: string
}

export interface VoucherCodeGenerateParams {
  cartPriceRule: number
  amount: number
  length: number
  format: 'alphanumeric' | 'alphabetic' | 'numeric'
  prefix?: string
  suffix?: string
  hyphensOn?: number
}
