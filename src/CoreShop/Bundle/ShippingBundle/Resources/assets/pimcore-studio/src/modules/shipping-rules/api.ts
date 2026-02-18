/**
 * CoreShop ShippingBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { RuleApi, type Rule } from '@coreshop/rule/src/rules'

export interface ShippingRuleDetail extends Rule {
  id?: number
  name: string
  active?: boolean
}

export const shippingRuleApi = new RuleApi<ShippingRuleDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/shipping_rules'
})
