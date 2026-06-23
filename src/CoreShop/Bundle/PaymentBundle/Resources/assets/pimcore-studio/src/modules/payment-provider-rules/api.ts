/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { RuleApi } from '@coreshop/rule/src/rules'
import type { PaymentProviderRule } from './types'

export class PaymentProviderRuleApi extends RuleApi<PaymentProviderRule> {
}

export const paymentProviderRuleApi = new PaymentProviderRuleApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/payment_provider_rules'
})
