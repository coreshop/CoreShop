/**
 * CoreShop PaymentBundle - Payment Provider Rule Group Widget
 *
 * Schema form widget that renders PaymentProviderRuleGroupPanel for the
 * 'payment_provider_rule_group_collection' block prefix.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { PaymentProviderRuleGroupPanel } from '../PaymentProviderRuleGroupPanel'
import type { PaymentProviderRuleGroup } from '../api'

interface PaymentProviderRuleGroupWidgetProps {
  value?: PaymentProviderRuleGroup[]
  onChange?: (value: PaymentProviderRuleGroup[]) => void
}

export const PaymentProviderRuleGroupWidget: React.FC<PaymentProviderRuleGroupWidgetProps> = ({
  value,
  onChange,
}) => {
  return (
    <PaymentProviderRuleGroupPanel
      ruleGroups={value ?? []}
      onChange={(ruleGroups) => onChange?.(ruleGroups)}
    />
  )
}
